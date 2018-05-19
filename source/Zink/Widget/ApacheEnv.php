<?php

/**
 * apache 工具类
 * LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" \"%{APK_UID}e\" \"%{CID}e\" \"%{APK_SESSIONID}e\" \"%{APK_CID}e\" \"%{APK_APINAME}e\" \"%{APK_APIVERSION}e\" %{APK_TIME}e %{APK_MEMORY}e \"%{APK_TIME_TAG}e\" \"%{APK_MEMORY_TAG}e\""
 */

namespace Zink\Widget;

class ApacheEnv
{

    const APK_CID = 'APK_CID';             // 同header里面的cid，默认cid会转存为激活后计算的cid
    const APK_UID = 'APK_UID'; 
    const APK_TIME = 'APK_TIME';            // 一次正常请求处理耗时(毫秒）
    const APK_MEMORY = 'APK_MEMORY';            // 一次正常请求处理内存消耗（k）
    const APK_TIME_TRACE = 'APK_TIME_TRACE';            // 一次正常请求处理耗时统计
    const APK_MEMORY_TRACE = 'APK_MEMORY_TRACE';            // 一次正常请求处理内存消耗统计
    const APK_APINAME = 'APK_APINAME';                // 接口名称
    const APK_APIVERSION = 'APK_APIVERSION';          // 接口版本
    const APK_SESSIONID = 'APK_SESSIONID';            // session id

    /**
     * 设置环境变量，可以写入apache日志中
     * @param type $key
     * @param type $value
     * @return type 
     */

    public static function set($key, $value)
    {
        if(!function_exists('apache_setenv')){
            return false;
        }
        
        return apache_setenv($key, $value);
    }

    public static function get($key)
    {
        if(!function_exists('apache_getenv')){
            return false;
        }
        
        return apache_getenv($key);
    }

    public static function setTimeTrace($timeTag)
    {
        return self::set(self::APK_TIME_TRACE, $timeTag);
    }

    public static function setTime($time)
    {
        return self::set(self::APK_TIME, $time);
    }

    public static function setMemoryTrace($memoryTag)
    {
        return self::set(self::APK_MEMORY_TRACE, $memoryTag);
    }

    public static function setMemory($memory)
    {
        return self::set(self::APK_MEMORY, $memory);
    }

    public static function setApiName($name)
    {
        return self::set(self::APK_APINAME, $name);
    }

    public static function setApiVersion($v)
    {
        return self::set(self::APK_APIVERSION, $v);
    }

    public static function setSessionId($sid)
    {
        return self::set(self::APK_SESSIONID, $sid);
    }

    public static function setUid($uid)
    {
        return self::set(self::APK_UID, $uid);
    }

    public static function setCid($cid)
    {
        return self::set(self::APK_CID, $cid);
    }
}
