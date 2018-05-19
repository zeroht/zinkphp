<?php
/**
 * 路由控制器类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/9 @thu: 创建；
 */

namespace Zink\Core;
use Common\Constant;
use Zink\Exception\RuntimeException;
use Zink\Widget\ApacheEnv;
use Zink\Widget\Request;

class Router

{
    /*
     * url规则
     * /{cname}/{action}
     * /{cname}/{action}?{params}
     */

    const METHOD_SUFFIX = 'Action';
    /* 标准url 规则 */
    const URL_RULE = '/\A\/([a-zA-Z][\w\-\/]*)\/([a-zA-Z][\w\-]*)(|\?.*)\z/';

    protected static $_action = '';
    protected static $_controller = null;
    protected static $_method = null;
    protected static $_params = null;
    protected static $_tpl_file = '';   // 默认模板文件

    /**
     * 判断一个方法是否为Action方法
     * @param $method
     * @param string $actionName
     * @return bool
     */
    public static function isActionMethod($method, &$actionName = '')
    {
        if (preg_match('/^(.+)'.self::METHOD_SUFFIX.'$/i', $method, $matchs)){
            $actionName = strtolower($matchs[1]);
            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * 获取action名，eq: /Admin/login ; /user/info/edit
     * @return type
     */
    public static function getAction()
    {
        return self::$_action;
    }

    /**
     * 获取当前controller实例
     * @return \Zink\Core\Controller
     */
    public static function getController()
    {
        return self::$_controller;
    }

    /**
     * 获取当前action的方法名
     * @return type
     */
    public static function getMethod()
    {
        return self::$_method;
    }

    /**
     * 返回默认模板文件名
     * @param bool $suffix
     * @return string
     */
    public static function getTplFile($suffix = true)
    {
        $tpl = self::$_tpl_file;
        if ($suffix){
            $tpl .= Constant::get('TPL_FILE_SUFFIX');
        }
        
        return $tpl;
    }
    
    /**
     * 解析uri
     * 子类可以根据各自的url规则重写此类
     * @param $uri
     * @param $host
     * @return array|bool
     */
    public static function parseRequest($uri, $host)
    {
        /* 加载自定义的rewrite规则，支持根据host配置 */
        $rewrite = Config::loadRewrite($host);

        $url = $uri;
        /* 处理url rewrite 规则 */
        foreach ($rewrite as $regex => $replacement) {
            $pattern = '/'.$regex.'/i';
            if (preg_match($pattern, $uri)) {
                /* 
                 * 获取rewrite后的url
                 * 把$replacement增加的参数带入处理
                 */

                $url = preg_replace($pattern, $replacement, $uri);
                if (preg_match('/^http/i', $url) || is_file(APP_PATH.$url)){
                    // 跳转到第三方页面或当前App的其他页面
                    App::redirect($uri);
                    return TRUE;
                }

                break;
            }
        }

        /* 解析url */
        if (!preg_match(self::URL_RULE, $url, $matches)) {
            return FALSE;
        }

        /**
         * 这个params是 url rewrite 规则里面带入的默认参数，非实际的请求参数
         * 实际的请求参数在 ParameterInterceptor 拦截器中做过滤处理
         */
        $query = (0 === strpos($matches[3], '?')) ? substr($matches[3], 1) : '';
        parse_str($query, $params);
        return array(
            'controller' => strtolower($matches[1]),                 // 接口名: user; user/info
            'method' => strtolower($matches[2]),                     // 接口的方法名
            'params' => $params ? array_change_key_case($params) : null            // 参数名大小写无关
        );
    }

    /**
     * 解析路由
     * @param $uri
     * @param $host
     * @return bool
     * @throws RuntimeException
     */
    public static function dispatch($uri, $host)
    {
        $appClass = get_called_class();
        $request = $appClass::parseRequest($uri, $host);
        if (!$request) {
            return FALSE;
        }

        self::$_action = '/' . $request['controller'] . '/' . $request['method'];
        self::$_method = $request['method'] . self::METHOD_SUFFIX;         //添加后缀 Action
        self::$_params = $request['params'];
        self::$_tpl_file = $request['controller'] . '/' . $request['method'];

        $class = Config::loadController($request['controller']);
        $appName = Config::getAppName();
        $appClass = "\\App\\{$appName}\\Controller\\{$class}";
        $commonClass = "\\Common\\Controller\\{$class}";
        /* 加载控制器类文件 */
        if (Autoloader::import($appClass)) {
            $actionClass = $appClass;
        }else if (Autoloader::import($commonClass)) {
            $actionClass = $commonClass;
        }else {
            Log::getLogger()->error('Action '.self::$_action.' Not Exist. ');
            return FALSE;
        }

        if (!is_subclass_of($actionClass, Controller::class)){
            throw new RuntimeException("$actionClass is not instanceof Controller");
        }
        
        if (!class_exists($actionClass) || !method_exists($actionClass, self::$_method)) {
            Log::getLogger()->error('Action Class '.$actionClass.' Not Exist. ');
            return FALSE;
        }
        
        /* 设置apache 环境变量 */
        ApacheEnv::setApiName(self::$_action);
        /* 默认的模板存目录与action目录同名 */
        self::$_controller = new $actionClass(self::$_params);

        return TRUE;
    }
}

/* End of file Router.php */
