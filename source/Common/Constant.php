<?php
/**
 *
 * 加载 constant.php 通用配置文件
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Common;


use Zink\Core\Config;

class Constant
{
    private static $_consts = array();
    
    public static function get($key)
    {
        return self::$_consts[$key];
    }
    
    public static function load($appName)
    {
        Config::setAppName($appName);
        self::$_consts = Config::loadConstant();
    }
}

/* End of file Constant.php */
