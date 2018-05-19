<?php

/**
 * session service 
 */

namespace Common\Service;

use Common\Constant;
use Zink\Core\Session;
use Zink\Widget\ArrayList;

class SessionService extends Session
{

    public static function updateUserInfo($info)
    {
        $ui = self::getUserInfo();
        foreach ($info as $k => $v) {
            $ui[$k] = $v;
        }

        self::setUserInfo($ui);
    }

    public static function getUserInfo()
    {
        return parent::get(Constant::get('SESSION_KEY_USER'));
    }

    public static function setUserInfo($ui)
    {
        return parent::set(Constant::get('SESSION_KEY_USER'), $ui);
    }

    public static function getUid()
    {
        $ui = self::getUserInfo();
        return ArrayList::safeGet($ui, 'id', 0);
    }
}
