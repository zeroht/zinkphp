<?php

namespace Zink\Widget;

class Ip
{

    /**
     * 获取客户端IP地址
     * @return mixed
     */
    public static function getClientIp()
    {
        static $_ip = NULL;
        if ($_ip !== NULL) {
            return $_ip;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $tmp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = $tmp[0];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $tmp = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
            $ip = $tmp[0];
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = '0.0.0.0';
        }

        $_ip = $ip;
        return $ip;
    }
    
    public static function isIp($ip)
    {
        return preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $ip);
    }
    
    public static function ip2int($ip) {
        if (preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)\$/', $ip, $array)) {
            return (16777216 * $array[1] + 65536 * $array[2] + 256 * $array[3] + 1 * $array[4]);
        } else {
            return (0);
        }
    }
    
    /**
     * 通过淘宝IP接口获取IP地理位置
     * @param string $ip
     * @return: string
     * */
    public static function ip2city($ip) {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        // 设置超时限制的context
        $opts = array(
            'http' => array(
                'timeout' => 1
            )
        );
        
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        $ipinfo = json_decode($result, true);
        $city = isset($ipinfo['data']['city']) ? $ipinfo['data']['city'] : '';
        return str_replace("市", "", $city);
    }
}
