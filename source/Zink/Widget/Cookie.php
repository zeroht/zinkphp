<?php

namespace Zink\Widget;


class Cookie
{
    const TTL_HOUR = 3600;
    const TTL_DAY = 86400;
    const TTL_YEAR = 31536000;
    
    public static function setGloble($name, $value, $expire = 0)
    {
        $domain = Request::getCookieDomain();
        setcookie($name, $value, $expire, "/", $domain);
    }

    public static function set($name, $value, $expire = 0, $path = "",
            $domain = "")
    {
        setcookie($name, $value, $expire, $path, $domain);
    }

    public static function get($name)
    {
        return ArrayList::safeGet($_COOKIE, $name);
    }
}
