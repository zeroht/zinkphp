<?php
/**
 * Http Request
 *  参考：http://php.net/manual/zh/reserved.variables.server.php
 * http://127.0.0.1:8808/Admin/login?name=test&pwd=111?name=test&pwd=111
 *  [SCRIPT_URL] => /Admin/login
 *  [SCRIPT_URI] => http://127.0.0.1:8808/Admin/login
 *  [HTTP_HOST] => 127.0.0.1:8808
 *  [SERVER_NAME] => 127.0.0.1
 *  [SERVER_ADDR] => 127.0.0.1
 *  [SERVER_PORT] => 8808
 *  [REMOTE_ADDR] => 127.0.0.1
 *  [QUERY_STRING] => __app__=Admin&name=test&pwd=111
 *  [REQUEST_URI] => /Admin/login?name=test&pwd=111
 *  [SCRIPT_NAME] => /index.php
 *  [PHP_SELF] => /index.php
 *  注：以上是apache的打印结果,nginx的输出可以不同
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Widget;

class Request
{

    const STATUS_200 = 200;
    const STATUS_400 = 400;
    const STATUS_401 = 401;
    const STATUS_402 = 402;
    const STATUS_403 = 403;
    const STATUS_404 = 404;
    const STATUS_500 = 500;
    const STATUS_501 = 501;
    const STATUS_502 = 502;
    const STATUS_503 = 503;

    protected static $_statusCode = array(
        self::STATUS_200 => 'SUCCESS',
        self::STATUS_400 => 'BAD REQUEST',
        self::STATUS_401 => 'UNAUTHORIZED',
        self::STATUS_402 => 'NEED LOGIN',
        self::STATUS_403 => 'FORBIDDEN',
        self::STATUS_404 => 'NOT FOUND',
        self::STATUS_500 => 'REQUEST FAILED',
        self::STATUS_501 => 'PARAM NOT FOUND',
        self::STATUS_502 => 'INVALID PARAM',
        self::STATUS_503 => 'INTERNAL SERVER ERROR'
    );

    public static function isGet()
    {
        return ('get' == strtolower($_SERVER['REQUEST_METHOD']));
    }

    public static function isPost()
    {
        return ('post' == strtolower($_SERVER['REQUEST_METHOD']));
    }

    public static function isPut()
    {
        return ('put' == strtolower($_SERVER['REQUEST_METHOD']));
    }
    
    public static function isAjax()
    {
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return TRUE;
        }

       return FALSE;
    }

    /**
     * 自定义的请求头部,用于区分是否Ajax获取json数据
     * @return bool
     */
    public static function isAjaxJson()
    {
        if(isset($_SERVER['HTTP_AJAX']) &&  'json' == strtolower($_SERVER['HTTP_AJAX'])) {
            return TRUE;
        }

        return FALSE;
    }
    
    public static function isMockRequestFromApidoc()
    {
        $mock = isset($_SERVER['HTTP_MOCK']) ? $_SERVER['HTTP_MOCK'] : false;
        return ($mock == 'apidoc') ? TRUE : FALSE;
    }

    public static function isMockRequest()
    {
        return (isset($_SERVER['HTTP_MOCK']) && $_SERVER['HTTP_MOCK']);
    }


    public static function isStudentProduct()
    {
        return (isset($_SERVER['HTTP_PID']) && $_SERVER['HTTP_PID'] == 1);
    }

    public static function isTeacherProduct()
    {
        return (isset($_SERVER['HTTP_PID']) && $_SERVER['HTTP_PID'] == 2);
    }

    public static function isFromApp()
    {
        return isset($_SERVER['HTTP_CID']);
    }

    public static function isFromWeixin()
    {
        $ua = strtolower($_SERVER["HTTP_USER_AGENT"]);
        return (FALSE !== strpos($ua, 'micromessenger'));
    }

    public static function isFromWeibo()
    {
        $ua = strtolower($_SERVER["HTTP_USER_AGENT"]);
        return (FALSE !== strpos($ua, 'weibo'));
    }

    public static function isFromIphone()
    {
        $ua = strtolower($_SERVER["HTTP_USER_AGENT"]);
        return (FALSE !== strpos($ua, 'iphone'));
    }
       
    public static function isFromAndroid()
    {
        $ua = strtolower($_SERVER["HTTP_USER_AGENT"]);
        return (FALSE !== strpos($ua, 'android'));
    }
    
    public static function isSSL()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
            return TRUE;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 是否包含富文本数据提交
     * @return bool
     */
    public static function isRtpPost()
    {
        return (isset($_SERVER['HTTP_RTP']) && $_SERVER['HTTP_RTP']);
    }

    public static function getUri()
    {
        return $_SERVER['SCRIPT_URL'] ? $_SERVER['SCRIPT_URL'] : $_SERVER['REQUEST_URI'];
    }

    public static function getUrl()
    {
        $uri = $_SERVER['SCRIPT_URI'];
        $query = $_SERVER['QUERY_STRING'];
        return $query ? "{$uri}?{$query}" : $uri;
    }
    
    public static function getRefererHost()
    {
        $refer = $_SERVER["HTTP_REFERER"];
        $data = parse_url($refer);
        if ($data['port']){
            $host = $data['host'].':'.$data['port'];
        }else {
            $host = $data['host'];
        }

        return $host;
    }

    public static function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function getHeaders()
    {
        $_SERVER['HTTP_SOURCE'];
        $header = [];
        foreach ($_SERVER as $key => $value){
            if (preg_match('/^HTTP_(\w+)$/', $key, $matches)){
                $header[$matches[1]] = $value;
            }
        }
        
        return $header;
    }

    /**
     * 过滤非法字符
     * @param $data
     * @return mixed
     */
    public static function filterHtml($data)
    {
        if (is_array($data)){
            foreach ($data as &$val){
                $val = self::filterHtml($val);
            }

            return $data;
        }else {
            return htmlspecialchars(trim($data), ENT_NOQUOTES);
        }
    }

    public static function filterXss($data)
    {
        if (is_array($data)){
            foreach ($data as &$val){
                $val = self::filterXss($val);
            }

            return $data;
        }else {
            $newData = trim($data);
            $tags = ['script', 'a', 'link', 'iframe', 'style'];
            foreach ($tags as $tag) {
                $newData = preg_replace('/<'.$tag.'[\s\S]*?<\/'.$tag.'>/i', '', $newData);
            }
      
            return $newData;
        }
    }

    // URL重定向   
    public static function redirect($url)
    {
        ob_end_flush();
        header("HTTP/1.1 301 Moved Permanently");
        header("Location:$url");
        ob_start();
    }

}
/* End of file Request.php */