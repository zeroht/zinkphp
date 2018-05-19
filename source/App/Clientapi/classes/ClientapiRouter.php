<?php
/**
 * Clientapi路由控制器
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

namespace App\Clientapi;

use Zink\Core\Autoloader;
use Zink\Core\Config;
use Zink\Core\Log;
use Zink\Core\Router;
use Zink\Widget\ApacheEnv;
use Zink\Widget\ArrayList;
use Zink\Widget\Request;

class ClientapiRouter extends Router
{

    /**
     * 解析路由
     * @param $uri
     * @param $host
     * @return bool
     * @throws RuntimeException
     */
    public static function dispatch($uri, $host)
    {
        $request = parent::parseRequest($uri, $host);
        if (!$request) {
            return FALSE;
        }

        $version = ArrayList::safeGet($request['params'], 'v');
        if (empty($version)){
            return FALSE;
        }      

        self::$_action = '/' . $request['controller'] . '/' . $request['method'];
        self::$_method = $request['method'] . self::METHOD_SUFFIX;         //添加后缀 Action
        self::$_params = $request['params'];

        $class = Config::loadController($request['controller']);
        
        // 找接口版本或之前最新版本文件
        $vClass = "\\App\\Clientapi\\Controller\\".$class."_".str_replace('.', '_', $version); // Controller_1_0_0
        $class = str_replace('\\', '/', $class);
        $file = APP_PATH . "classes/Controller/v{$version}/{$class}.php";
        if(!Autoloader::import($file) || !class_exists($vClass) || !method_exists($vClass, self::$_method)){
            Log::getLogger()->error("invalid api:".  Request::getUrl());
            return FALSE;
        }
        
        /* 模板文件前缀 v1.0.0/app/active */
        self::$_tpl_file = strtolower('v'.$version.self::$_action);//.Constant::get('TPL_FILE_SUFFIX');
        // 设置apache 变量，记录apache 日志
        ApacheEnv::setApiName(self::$_action);
        ApacheEnv::setApiVersion($version);

        /* 默认的模板存目录与action目录同名 */
        self::$_controller = new $vClass(self::$_params);

        return TRUE;
    }
}

/* End of file ClientapiRouter.php */
