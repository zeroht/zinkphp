<?php
/**
 *  Debuger 调试类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Zink\Core;

use Common\Constant;
use Zink\Widget\IteratorSupport;
use Zink\Widget\JavaScript;
use Zink\Widget\Json;
use Zink\Core\Log;
use Zink\Db\DB;

class Debugger
{
    const TAG_BEGIN = "tag_begin";
    const TAG_END = "tag_end";
    
    /**
     * 是否开发模式
     * @return bool
     */
    public static function isDevelopMode()
    {
        return (defined('Z_ENVIRONMENT') && Z_ENVIRONMENT == 'develop');
    }

    /**
     * 是否测试模式
     * @return bool
     */
    public static function isTestMode()
    {
        return (defined('Z_ENVIRONMENT') && Z_ENVIRONMENT == 'test');
    }

    /**
     * 是否仿真模式
     * @return bool
     */
    public static function isSimMode()
    {
        return (defined('Z_ENVIRONMENT') && Z_ENVIRONMENT == 'sim');
    }

    /**
     * 是否发布模式
     * @return bool
     */
    public static function isReleaseMode()
    {
        return (defined('Z_ENVIRONMENT') && Z_ENVIRONMENT == 'release');
    }

    /**
     * 是否线上模式
     * @return bool
     */
    public static function isOnlineMode()
    {
        return (!defined('Z_ENVIRONMENT') || Z_ENVIRONMENT == 'online');
    }


    public static function getCurrentMode()
    {
        if (defined('Z_ENVIRONMENT')) {
            return Z_ENVIRONMENT;
        }
        
        return 'online';
    }

    /**
     * 输出到页面
     * @param $msg
     */
    public static function output($msg)
    {
        if (!self::isDevelopMode()) {
            return;
        }

        if (is_string ($msg)){
            echo $msg;
        } elseif ($msg instanceof IteratorSupport) {
            print_r($msg->toArray());
        } elseif (is_array($msg)) {
            print_r($msg);
        } else {
            var_dump($msg);
        }
    }

    /**
     * 输出到浏览器的控制台
     * @param $msg
     */
    public static function console($msg)
    {
        if (self::isOnlineMode()) {
            return;
        }
        
        $javascript = new JavaScript();
        $javascript->appendLog($msg)->display();
    }

    /**
     * 打印并终止执行
     * @param $msg
     */
    public static function halt($msg)
    {
        if (!self::isDevelopMode()) {
            return;
        }
        self::output($msg);
        die;
    }

    /**
     * 开启xhporf性能分析
     */
    public static function startXhprof()
    {
        if (!Constant::get('DEBUG_XHPROF_ENABLED')) {
            return False;
        }
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    }

    /**
     * 终止xhprof分析，并生成分析报告
     */
    public static function stopXhprof()
    {
        if (!Constant::get('DEBUG_XHPROF_ENABLED')) {
            return False;
        }

        require_once ROOT_PATH . "Lib/xhprof_lib/utils/xhprof_lib.php";
        require_once ROOT_PATH . "Lib/xhprof_lib/utils/xhprof_runs.php";

        $data = xhprof_disable();
        $time = $data['main()']['wt'] / 1000;
        $objXhprofRun = new \XHProfRuns_Default();
        $run_id = $objXhprofRun->save_run($data, "xhprof");

        //记录日志
        //$content = sprintf(" Action:[%s] COST:[%s ms] RUN_ID:[%s]", $_SERVER['REQUEST_URI'], $time, $run_id);
        //Log::getLogger("xhprof")->debug($content);

        //写入数据库
        $db = DB::create();
        $sql = sprintf("INSERT INTO xhprof_log (`run_id`,`cost`,`action`) VALUES('%s','%s','%s')", $run_id, (int)$time, $_SERVER['REQUEST_URI']);
        $db->runSql($sql);
    }

    /**
     * 返回当前的 Unix 时间戳以及微秒数
     * @return type
     */
    public static function microtime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * 是否开启性能追踪模式
     * @return boolean
     */
    public static function isTraceOpen()
    {
       /* if ((defined('__ZINK_DEBUG__TRACE__') && __ZINK_DEBUG__TRACE__)) {
            return true;
        }
*/
        return false;
    }

    /**
     * 是否开启js控制台性能追踪模式
     * @return boolean
     */
    public static function isJsTrace()
    {
       /* if(defined('__ZINK_DEBUG__AUTHCODE__') && $_GET['_zink_debug_trace_'] == __ZINK_DEBUG__AUTHCODE__){
            return true;
        }
*/
        return false;
    }

    /**
     * 记录各节点的时间和内存信息，方便输出调试
     * @param type $start：起点位置（不区分大小写）
     * @param type $end：结束点位置（不区分大小写）
     * @return type
     */
    public static function trace($start = '', $end = '')
    {
        if (self::isTraceOpen()) {
            // url中加入上述参数，会在js控制台中打印 trace信息
            $time = self::traceTime($start, $end);
            $mem = self::traceMemory($start, $end);
            if (empty($start) && empty($end)) {
                return array('time' => $time, 'memory' => $mem);
            }

            return true;
        }

        return false;
    }

    /**
     * 输出调试信息
     */
    public static function printTrace()
    {
        if (self::isTraceOpen()){
            /*
             * 追踪统计代码中各个部分耗时内存的。
             
            $js = new \Zink\Common\JavaScript();
            $js->appendLog("Time Trace:");
            $trace = self::trace();

            // 时间统计
            $timeArr = $trace['time'];
            $offset = current($timeArr);
            $values = array();
            foreach ($timeArr as $n => $t){
                $values[] = number_format(($t - $offset) * 1000); // 毫秒值
                $offset = $t;
                $js->appendLog("$n:$t");
            }

            \Zink\Util\ApacheEnv::setTimeTrace(implode(',', $values));

            // 内存统计
            $js->appendLog("Memory Trace:");
            $memArr = $trace['memory'];
            $values = array();
            $offset = current($memArr);
            foreach ($memArr as $n => &$m){
                $values[] = number_format(($m - $offset) / 1024); // k
                $offset = $m;
                $js->appendLog("$n:$m");
            }

            \Zink\Util\ApacheEnv::setMemoryTrace(implode(',', array_values($memArr)));

            if (self::isJsTrace()){
                // 输出js代码
                echo $js->toString();
            }*/
        }
    }

    /**
     * 记录和统计时间使用情况
     * @param string $start 开始标签
     * @param string $end 结束标签
     * @return mixed
     */
    public static function traceTime($start, $end = '')
    {
        $start = strtolower($start);
        $end = strtolower($end);
        static $_info = array();
        if (empty($start) && empty($end)) {
            // 返回所有记录
            return $_info;
        }

        if (empty($end)) { // 记录时间
            $_info[$start] = self::microtime();
        } else { // 统计时间
            if (!isset($_info[$end])) {
                $_info[$end] = self::microtime();
            }

            // 毫秒值（整数）
            return number_format(($_info[$end] - $_info[$start]) * 1000, 0, '.',
                '');
        }
    }

    /**
     * 记录和统计内存使用情况
     * @param string $start 开始标签
     * @param string $end 结束标签
     * @return mixed
     */
    public static function traceMemory($start, $end = '')
    {
        $start = strtolower($start);
        $end = strtolower($end);
        static $_mem = array();
        if (empty($start) && empty($end)) {
            // 返回所有记录
            return $_mem;
        }

        if (empty($end)) { // 记录时间和内存使用
            $_mem[$start] = memory_get_usage();
        } else { // 统计时间
            if (!isset($_mem[$end])) {
                $_mem[$end] = memory_get_usage();
            }

            // 单位（K）
            return number_format(($_mem[$end] - $_mem[$start]) / 1024, 0, '.',
                '');
        }
    }

    public static function pause($microsecond)
    {
        $randomMicrosecond = rand(0, $microsecond);
        usleep($randomMicrosecond);
    }
}

/* End of file Debugger.php */