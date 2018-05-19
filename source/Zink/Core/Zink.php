<?php
/**
 * 一些基础类库及接口的定义
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Core;
use Zink\Db\DB;

/**
 * Interface Interceptor
 * 所有拦截器都需实现此接口中的'intercept'方法。
 * @package Zink\Core
 */
interface Interceptor
{
    /**
     * 拦截器函数
     * @param Action $action
     * @return mixed
     */
    public function intercept(Action &$action);
}

/**
 * Pdo 预处理执行
 * Interface SqlStatement
 * @package Zink\Core
 */
interface SqlStatement
{
    /**
     * @return array [$statement, $binds] Pdo 预处理执行的语句级变量值
     */
    public function toQuery();
}

/**
 * Class DbSupport
 * @package Zink\Core
 */
trait DbSupport
{
    /**
     * 默认为配置文件配置的数据库
     * @type null 数据库名
     */
    protected $_dbName = null;

    /**
     * @type null 表名
     */
    protected $_table = null;

    /**
     * @type null|DB DB实例
     */
    protected $_db = null;

    /**
     * 初始化db实例
     */
    protected function _initDB()
    {
        $this->_db = DB::create($this->_table, $this->_dbName);
    }
}

/**
 * Class Singleton
 * @package Zink\Core
 */
class Singleton
{
    protected function __construct()
    {
        // 不允许外部 new
    }

    protected function __clone()
    {
        // 不允许外部 clone
    }

    protected static $_instances = [];

    /**
     * 创建单一实例
     * @return Singleton
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }

        return self::$_instances[$class];
    }
}

/**
 * Class Autoloader
 * @package Zink\Core
 */
class Autoloader
{
    /**
     * 自动加载类
     * @staticvar array $_load_cache
     * @param type $class
     * @return type
     */
    public static function autoload($class)
    {
        self::import(self::classToFilename($class));
    }

    public static function classToFilename($class)
    {
        if (is_file($class)){
            return $class;
        }

        $class_segs = explode("\\", trim($class, "\\"));
        $className = array_pop($class_segs);
        if ($class_segs && 'App' === $class_segs[0] && count($class_segs) >= 2) {
            $path1 = implode("/", array_slice($class_segs, 0, 2));
            $path2 = implode("/", array_slice($class_segs, 2));
            $filename = ROOT_PATH . "/"  . $path1 ."/classes/" . $path2 . "/" . $className . '.php';
        } else{
            $filename = ROOT_PATH . implode("/", $class_segs) . "/" . $className . '.php';
        }

        return $filename;
    }

    /**
     * 加载类文件
     * @staticvar array $_importFiles
     * @param string $filename
     * @return boolean
     */
    public static function import($filename)
    {
        $filename = self::classToFilename($filename);
        $filename = str_replace('\\', '/', $filename);
        $filename = str_replace('//', '/', $filename);
        static $_importFiles = array();
        if (!isset($_importFiles[$filename])) {
            if (is_file($filename)) {
                include_once $filename;
                $_importFiles[$filename] = true;
            } else {
                $_importFiles[$filename] = false;
            }
        }

        return $_importFiles[$filename];
    }

}

/* End of file Zink.php */
