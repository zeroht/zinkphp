<?php
/**
 * 应用程序类 执行应用过程管理
 *  不同的App应用可以
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Zink\Core;
use Common\Constant;
use Zink\Exception\NotFoundException;
use Zink\Exception\RuntimeException;
use Zink\Exception\RequestException;
use Zink\View\AbstractView;
use Zink\Widget\ApacheEnv;
use Zink\Widget\Request;
use Zink\Widget\Variable;

class App
{
    private static $_finished = false;

    /**
     * 应用程序初始化
     * @throws ZRuntimeException
     */
    static public function start()
    {
        Debugger::startXhprof();

        $appClass = get_called_class();

        // 设定错误和异常处理
        register_shutdown_function(array($appClass, 'shutdownError'));
        set_error_handler(array($appClass, 'appError'), E_ERROR | E_NOTICE);
        set_exception_handler(array($appClass, 'appException'));

        // 运行应用
        try {
            $appClass::init();
            $appClass::exec();
        } catch (\Exception $e) {
            $appClass::appException($e);
        }

        $appClass::finish();
    }
    
    /**
     * 应用程序初始化
     * @throws NotFoundException
     * @throws RuntimeException
     */
    static public function init()
    {
        // 定义当前请求的系统常量
        define('IS_GET', Request::isGet());
        define('IS_POST', Request::isPost());
        define('IS_PUT', Request::isPut());
        define('IS_AJAX', Request::isAjax());
        define('IS_WEIXIN', Request::isFromWeixin());
        define('IS_IOS', Request::isFromIphone());
        define('IS_ANDROID', Request::isFromAndroid());

        // URL调度
        $uri = Request::getUri();
        $host = Request::getHost();
        if (!Router::dispatch($uri, $host)) {
            throw new NotFoundException("invalid request: $uri");
        }

        // TODO: 初次访问时 COOKIE无SID，将Session过期时间设置短点，避免无效session浪费内存空间
        $name = Constant::get('SESSION_NAME');
        $sid = $_COOKIE[$name];
        $expire = $sid ? null : 15;
        Session::startSession($sid, $expire);
    }

    /**
     * 执行现有的action方法
     * @throws ErrorException
     * @throws RequestException
     */
    public static function exec()
    {
        $controller = Router::getController();
        $method = Router::getMethod();
        $action = Router::getAction();
        if (is_subclass_of($controller, Controller::class)) {
            $invocation = new Action($controller, $method, $action);
            $view = $invocation->invoke();
            if (is_subclass_of($view, AbstractView::class)) {
                /* 直接返回视图 */
                $buffer = ob_get_clean();

                header('Last-Modified:' . gmdate('D,d M Y H:i:s') . ' GMT');
                header('Cache-Control:no-cache,no-store,must-revalidate');
                header('Cache-Control:post-check=0,pre-check=0', false);
                header('Pragma:no-cache');
                header('Expires: 0');
                $view->display();

                Debugger::output($buffer);     // 输出调试信息
            } else {
                throw new RuntimeException("Invaid View：" . Request::getUri());
            }
        } else {
            throw new RequestException("invalid action:" . Request::getUri());
        }
    }

    
    /**
     * 请求转发
     * @param type $url
     */
    public static function redirect($url)
    {
        Request::redirect($url);
        $appClass = get_called_class();
        $appClass::finish();
    }

    /**
     * 正常结束
     * @param int $status
     */
    public static function finish($status = 0)
    {
        self::$_finished = true;

        Debugger::stopXhprof();

        /* 一次完整请求 php的耗时和内存使用 */
        $timespan = Debugger::traceTime(Debugger::TAG_BEGIN, Debugger::TAG_END);    // 正常请求耗时时间（毫秒)
        $memspan = Debugger::traceMemory(Debugger::TAG_BEGIN, Debugger::TAG_END);    // 正常请求耗时内存（k)

        ApacheEnv::setTime($timespan);     // apache日志记录
        ApacheEnv::setMemory($memspan);     // apache日志记录

        //ZDebugger::printTrace();

        exit($status);
    }
    
    /**
     * 自定义异常处理
     * @access public
     * @param Exception $e 异常对象
     */
    static public function appException($e)
    {
        if ($e instanceof Exception) {
            $e->log();
        } else {
            Log::getLogger()->fatal('appException'.$e->getMessage());
        }

        self::redirect(Constant::get('ERROR_PAGE'));
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline)
    {
        $message = "[appError({$errno})]{$errfile}({$errline}):{$errstr}";
        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                Log::getLogger()->error($message);
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                Log::getLogger()->warning($message);
                break;
        }
    }

    /**
     * 脚本执行完成或意外死掉导致PHP执行即将关闭时
     */
    static public function shutdownError()
    {
        if (!self::$_finished){
            // 非正常结束
            $e = error_get_last();
            $appClass = get_called_class();

            if ($e instanceof \Exception) {
                $appClass::appException($e);
            }else if (is_array ($e)){
                Log::getLogger()->fatal("unexpected shutdown: {$e['file']}({$e['line']}):{$e['message']}");
            }

            $appClass::finish();
        }
    }
}

/* End of file App.php */
