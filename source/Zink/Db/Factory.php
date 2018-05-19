<?php
/**
 * 数据库实例工厂
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/14 @thu: 创建；
 */

namespace Zink\Db;

use Zink\Core\Config;
use Zink\Db\Driver\Mysql;
use Zink\Db\Result\Record;
use Zink\Exception\RuntimeException;

class Factory
{

    const DRIVER_MYSQL = 'mysql';

    private static $_links = array();

    /**
     * 创建数据库连接实例
     * @param null $dbName 数据库名
     * @return Driver
     * @throws RuntimeException
     */
    public static function createLink(&$dbName = null)
    {
        $cfg = Config::loadDatabase($dbName);
        /* 每个数据库公用一个连接 */
        if (!isset(self::$_links[$dbName])) {
            $driver = strtolower($cfg['driver']);
            if ($driver == self::DRIVER_MYSQL) {
                self::$_links[$dbName] = new Mysql($cfg);
            } else {
                throw new RuntimeException("PdoDriver $driver Not Existed.");
            }
        }

        return self::$_links[$dbName];
    }

    /**
     * 创建数据对象
     * @param $data
     * @param null $pdoName 可以是完整的class类名,也可以是名字
     * @param null $namespace
     * @return Record
     */
    public static function createRecord($data, $pdoName = null, $namespace = null)
    {
        if (!$pdoName) {
            return new Record($data, true);
        }

        if (class_exists($pdoName)){
            return new $pdoName($data, true);
        }
        
        if (!$namespace) {
            $className = "\\Common\\Model\\Pdo\\{$pdoName}";
        } else {
            $className = "\\Common\\Model\\Pdo\\{$namespace}\\{$pdoName}";
        }
        
        if (class_exists($className)) {
            return new $className($data, true);
        }
        
        return new Record($data, true);
    }

}

/* End of file DBFactory.class.php */
