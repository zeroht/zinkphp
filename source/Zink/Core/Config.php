<?php
/**
 * 配置文件加载类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Zink\Core;


use Zink\Widget\Str;

class Config
{
    const CFG_ACCESS = 'access.php';                // 访问频度控制文件
    const CFG_CONSTANT = 'constant.php';            // 基本配置文件
    const CFG_CONTROLLER = 'controller.php';        // 控制器配置文件
    const CFG_DATABASE = 'database.php';            // 数据库配置文件
    const CFG_INTERCEPTOR = 'interceptor.php';      // 拦截器配置文件
    const CFG_LOG = 'log.php';                      // 日志配置文件
    const CFG_REWRITE = 'rewrite.php';              // url rewrite配置文件
    const CFG_SMARTY = 'smarty.php';                // smarty 配置文件

    protected static $_appName = APP_NAME;
    protected static $_appCfgRoot = APP_PATH.'/classes/Config/';
    
    protected static $_config = array();

    /**
     * 设置当前的 appName(不可用APP_NAME代替，innerapi本地调用时，$appName会变化）
     * @param $appName
     */
    public static function setAppName($appName)
    {
        self::$_appName = $appName;
        self::$_appCfgRoot = APP_ROOT.$appName.'/classes/Config/';

        // 清空缓存
        self::$_config = array();
    }
    
    public static function getAppName()
    {
        return self::$_appName;
    }
    
    /**
     * 获取配置文件加载列表
     * @param type $cfgFileName：配置文件名
     * @return array: 文件列表 
     */
    protected static function _getConfigFiles($cfgFileName)
    {
        $cfgFiles = array();
        // load common config
        $commonCfgRoot = COMMON_PATH . 'Config/';
        $file = $commonCfgRoot . $cfgFileName;
        if (is_file($file)) {
            $cfgFiles[] = $file;
        }

        // load app's config
        $file = self::$_appCfgRoot . $cfgFileName;
        if (is_file($file)) {
            $cfgFiles[] = $file;
        }

        // load environment config
        if (defined('Z_ENVIRONMENT')){
            // load common's environment config
            $file = $commonCfgRoot . Z_ENVIRONMENT . '/' . $cfgFileName;
            if (is_file($file)) {
                $cfgFiles[] = $file;
            }

            // load app's environment config
            $file = self::$_appCfgRoot . Z_ENVIRONMENT . '/' . $cfgFileName;
            if (is_file($file)) {
                $cfgFiles[] = $file;
            }
        }

        return $cfgFiles;
    }

    /**
     * 将隐藏配置替换为真实数据
     * @param array $confgArr
     * @return array
     */
    protected static function _parseEnvVal(array $confgArr)
    {
        static $_env = array();
        if (empty($_env)){
            if (is_file(APP_PATH.'.env')){
                $_env = parse_ini_file(APP_PATH.'.env');
            }else {
                $_env = parse_ini_file(APP_ROOT.'.env');
            }
        }

        foreach ($confgArr as $key => &$value){
            if (is_array($value)){
                $value = self::_parseEnvVal($value);
            }else if (preg_match('/^\{(\w+)\}$/', $value, $matchs)){
                $value = $_env[$matchs[1]];
            }
        }

        return $confgArr;
    }

    /**
     * 加载数组型配置文件
     * @param type $cfgFileName
     * @param boolean $keytolower：是否将数组的一级key转成小写，默认false
     * @return type 
     */
    protected static function _loadArrayConfig($cfgFileName, $keytolower = false)
    {
        if (isset(self::$_config[$cfgFileName])) {
            return self::$_config[$cfgFileName];
        }

        $cfgFiles = self::_getConfigFiles($cfgFileName);
        $cfg = array();
        foreach ($cfgFiles as $file) {
            $extCfg = require($file);
            if ($keytolower) {
                $extCfg = array_change_key_case($extCfg);
            }

            /* 同属性覆盖 */
            $cfg = array_merge($cfg, $extCfg);
        }

        if ($cfgFileName == self::CFG_CONSTANT || $cfgFileName == self::CFG_DATABASE){
            $cfg = self::_parseEnvVal($cfg);
        }

        self::$_config[$cfgFileName] = $cfg;
        return self::$_config[$cfgFileName];
    }

    /**
     * 加载通用配置文件
     * @return boolean 
     */
    public static function loadConstant()
    {
        return self::_loadArrayConfig(self::CFG_CONSTANT, false);
    }

    /**
     * 加载数据库对应的配置文件
     * @param type $dbName：数据库名；引用返回正确的数据库名
     * @return array
     */
    public static function loadDatabase(&$dbName = null)
    {
        $key = self::CFG_DATABASE . '.' . $dbName;
        if ($dbName && isset(self::$_config[$key])) {
            return self::$_config[$key];
        }

        $config = self::_loadArrayConfig(self::CFG_DATABASE, true);
        if ($dbName && isset($config['connector'][$dbName])) {
            /* 对应的配置文件存在 */
            $dbCfg = array_change_key_case($config['connector'][$dbName]);
            $dbCfg['db_name'] = $dbName;
            if (empty($dbCfg['db_namespace'])) {
                $dbCfg['db_namespace'] = $dbName;
            }

            $config = array_merge($config, $dbCfg); // 合并配置
        } else {
            /* 赋值，引用返回默认数据库名 */
            $dbName = $config['db_name'];
        }

        unset($config['connector']);

        /* 缓存数据库对应的配置 */
        $key = self::CFG_DATABASE . '.' . $dbName;
        self::$_config[$key] = $config;
        return $config;
    }

    /**
     * 加载 日志 配置文件
     * @return array 
     */
    public static function loadLog()
    {
        return self::_loadArrayConfig(self::CFG_LOG, true);
    }

    /**
     * 加载Smarty配置文件 
     * @return array 
     */
    public static function loadSmarty()
    {
        $appName = self::$_appName;
        $key = self::CFG_SMARTY . '.' . $appName;
        if ($appName && isset(self::$_config[$key])) {
            return self::$_config[$key];
        }

        $cfg = self::_loadArrayConfig(self::CFG_SMARTY, true);
        if (!empty($cfg['default_val']) && !empty($cfg[$appName . '_val'])) {
            $cfg['default_val'] = array_merge($cfg['default_val'],
                    $cfg[$appName . '_val']);
            unset($cfg[$appName . '_val']);
        }

        self::$_config[$key] = $cfg;
        return $cfg;
    }

    /**
     * 加载app的rewrite配置文件 
     * @param type $host：当前访问的host域名
     * @return array 
     */
    public static function loadRewrite($host = null)
    {
        if (isset(self::$_config[self::CFG_REWRITE])) {
            return self::$_config[self::CFG_REWRITE];
        }

        $cfg = array();
        /* 加载app应用下的配置 */
        $file = self::$_appCfgRoot . self::CFG_REWRITE;
        if (is_file($file)) {
            $cfg = require($file);
        }

        /* 测试环境下的覆盖上面的cfg文件 */
        if (defined('Z_ENVIRONMENT') && is_file(self::$_appCfgRoot . Z_ENVIRONMENT . '/' . self::CFG_REWRITE)) {
            $cfg = require(self::$_appCfgRoot . Z_ENVIRONMENT . '/' . self::CFG_REWRITE);
        }

        /* 默认的rewrite规则，适用所有host */
        $rewriteRules = is_array($cfg['_default_']) ? $cfg['_default_'] : array();
        unset($cfg['_default_']);

        if ($host) {
            foreach ($cfg as $regex => $rules) {
                // 正则匹配，兼顾端口访问时测试环境和线上环境ip或域名不同的情况
                $pattern = '/' . $regex . '/i';
                if (is_array($rules) && preg_match($pattern, $host)) {
                    // 匹配到一个即可，不同应用host不同'
                    $rewriteRules = $rules + $rewriteRules; // 插入前面
                    break;
                }
            }
        }

        self::$_config[self::CFG_REWRITE] = $rewriteRules;
        return self::$_config[self::CFG_REWRITE];
    }

    /**
     * 加载 Controller 配置文件 
     * @param type $controller: controller 名称，不同应用的配置文件内容不同
     * @return array 
     */
    public static function loadController($controller = null)
    {
        $cfg = self::_loadArrayConfig(self::CFG_CONTROLLER, true);
        if ($controller && isset($cfg[$controller])) {
            // 返回controller对应的class文件
            return $cfg[$controller];
        } else {
            // 返回所有列表
            return $cfg;
        }
    }

    /**
     * 加载action的过滤器配置文件
     * @param type $action：action名，eq: /Admin/login
     * @param type $cfgName：配置文件名
     * @param type $delimiter：过滤器分隔符，默认为','
     * @param type $clearStart：移除过滤器符号，默认为'-'
     * @return type
     */
    protected static function _loadFilter($action, $cfgName, $delimiter = ',',
            $clearStart = '-')
    {
        if (empty($action)) {
            return null;
        }

        $action = strtolower($action);
        $rules = self::_loadArrayConfig($cfgName, true);
        $filters = array();
        /* 正则匹配 */
        foreach ($rules as $regex => $filter) {
            $pattern = '/'.$regex.'/i';
            if (preg_match($pattern, $action)) {
                $filters = self::_filterExtends($filters, $filter, $delimiter,
                                $clearStart);
            }
        }
        return array_values($filters);
    }

    /**
     * 字符串扩展：将扩展字符串追加到源字符串中，用$delimiter连接，
     * 用$delimiter切分字符串为数组，遍历数组，移除前面以$clearStart开头
     * 的字符串（不含$clearStart符号），同时移除自身；去重数组，最后用
     * $delimiter连接
     * @param type $srcStr：源字符串
     * @param type $extStr：扩展字符串
     * @param type $delimiter:字符串内的分割符；
     * @param type $clearStart：移除标记
     * @return string
     */
    protected static function _filterExtends($src, $ext, $delimiter = ',',
            $clearStart = '-')
    {
        $src = is_array($src) ? implode($delimiter, $src) : $src;
        $ext = is_array($ext) ? implode($delimiter, $ext) : $ext;
        if (empty($src) || empty($ext)) {
            $str = new Str($src . $ext);
            return $str->split($delimiter, true);
        }

        $string = $src . $delimiter . $ext;
        $arr = explode($delimiter, $string);
        $newArr = array();
        foreach ($arr as $value) {
            if (0 === strpos($value, $clearStart)) {
                $clearValue = substr($value, strlen($clearStart));
                $key = array_search($clearValue, $newArr);
                while (FALSE !== $key) {
                    unset($newArr[$key]);
                    $key = array_search($clearValue, $newArr);
                }
            } else {
                $newArr[] = $value;
            }
        }

        return array_unique($newArr);
    }

    /**
     * 加载应用拦截器配置
     * @param type $action
     * @return type
     */
    public static function loadInterceptor($action)
    {
        return self::_loadFilter($action, self::CFG_INTERCEPTOR);
    }

    /**
     * 加载访问频度控制文件
     * @param $uri
     * @return null|array
     */
    public static function loadAccess($uri)
    {
        $rules = self::_loadArrayConfig(self::CFG_ACCESS, true);
        return isset($rules[$uri]) ? $rules[$uri] : NULL;
    }
    
    protected static function _loadCurrentEnvironmentConfig($cfgName)
    {
        if (isset(self::$_config[$cfgName])) {
            return self::$_config[$cfgName];
        }

        if (defined('Z_ENVIRONMENT') && is_file(self::$_appCfgRoot . Z_ENVIRONMENT . '/' . $cfgName)) {
            $cfgFile = self::$_appCfgRoot . Z_ENVIRONMENT . '/' . $cfgName;
        } else {
            $cfgFile = self::$_appCfgRoot . $cfgName;
        }

        $cfg = require ($cfgFile);
        self::$_config[$cfgName] = $cfg;
        return $cfg;
    }
}

/* End of file Config.php */
