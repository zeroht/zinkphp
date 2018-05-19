<?php
/**
 * App 请求Header 工具类
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

namespace App\Clientapi\Widget;


class ApiHeader
{
    public static function isIos()
    {
        return self::getPlatform() == 'ios';
    }
    
    public static function isAndroid()
    {
        return self::getPlatform() == 'android';
    }


    public static function getProductId()
    {
        return intval($_SERVER['HTTP_PID']);
    }

    public static function getPlatform()
    {
        return strtolower($_SERVER['HTTP_PLATFORM']);
    }

    public static function getSessionId()
    {
        return $_SERVER['HTTP_SID'];
    }

    public static function getVersionId()
    {
        return intval($_SERVER['HTTP_VID']);
    }

    public static function getUid()
    {
        return $_SERVER['HTTP_UID'];
    }

    public static function getCid()
    {
        return $_SERVER['HTTP_CID'];
    }

    public static function getUa()
    {
        return $_SERVER['HTTP_UA'];
    }

    public static function getNetwork()
    {
        return strtolower($_SERVER['HTTP_NETWORK']);
    }
}
/* End of file ApiHeader.php */