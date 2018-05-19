<?php


/**
 * Innerapi 返回状态码定义
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/19 @thu: 创建；
 */

namespace Common;

class ErrorCode
{

    /**
     * Action方法默认返回值列表，参考http status code
     */
    /* 默认错误码 */
    const SUCCESS = 200;                        // 成功

    const ERR_302 = 302;                        // 跳转

    // 请求错误
    const ERR_401 = 401;                       // （未授权） 请求要求身份验证。
    const ERR_402 = 402;                       // 需要登录
    const ERR_403 = 403;                       // 禁止访问
    const ERR_404 = 404;                       // 非法请求，或资源不存在

    // url不合法或服务器异常
    const ERR_500 = 500;                       // 服务器遇到错误或bug引起，无法完成请求（服务器内部错误）
    const ERR_501 = 501;                       // 缺少必要参数
    const ERR_502 = 502;                       // 参数格式错误
    const ERR_503 = 503;                       // 服务异常（由于超载、读数据库超时、或停机维护等， 通常，这只是暂时状态）

    const ERR_513 = 513;                       // innerapi内部代码报错时使用,只可用于该场景,其他情况禁用该code
    /**
     * 扩展错误码,区分不同的业务提示
     * 一般App应对不同错误需要有不同逻辑处理时,使用如下错误码,否则统一使用500码
     */
    const ERR_600 = 600;
    const ERR_601 = 601;
    const ERR_602 = 602;
    const ERR_603 = 603;
    const ERR_604 = 604;
    const ERR_605 = 605;
    const ERR_606 = 606;
    const ERR_607 = 607;
    const ERR_608 = 608;
    const ERR_609 = 609;
    const ERR_610 = 610;   //用于状态改变刷新页面
    const ERR_611 = 611;   //用于状态改变关闭弹窗
    const ERR_612 = 612;    // 后台登陆页面弹窗



    /**
     * Code 对应的 Message
     * @type array
     */
    protected static $_errMsg = [
        self::SUCCESS => '请求成功',
        self::ERR_302 => '需要跳转',
        self::ERR_401 => '身份验证失败',
        self::ERR_402 => '需要登录',
        self::ERR_403 => '您没有此操作的权限',
        self::ERR_404 => '资源不存在',
        self::ERR_500 => '请求失败',
        self::ERR_501 => '缺少必要参数',
        self::ERR_502 => '参数格式错误',
        self::ERR_503 => '服务器异常',

        self::ERR_600 => '请求失败',
    ];

    public static function isValidCode($errcode)
    {
        if (isset(self::$_errMsg[$errcode])) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * 返回code对应的message
     * @param $errcode
     * @return string
     */
    public static function getMessage($errcode)
    {
        if (self::isValidCode($errcode)) {
            return self::$_errMsg[$errcode];
        } else {
            return 'Undefined Error Code';
        }
    }

    /**
     * 自定义 ERR_CODE_500 错误消息
     * @param null $msg
     * @return int
     */
    public static function err500($msg = null)
    {
        if (null != $msg) {
            self::$_errMsg[self::ERR_500] = $msg;
        }

        return self::ERR_500;
    }

    /**
     * 自定义 ERR_601 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err600($msg)
    {
        self::$_errMsg[self::ERR_600] = $msg;
        return self::ERR_600;
    }

    /**
     * 自定义 ERR_601 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err601($msg)
    {
        self::$_errMsg[self::ERR_601] = $msg;
        return self::ERR_601;
    }

    /**
     * 自定义 ERR_602 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err602($msg)
    {
        self::$_errMsg[self::ERR_602] = $msg;
        return self::ERR_602;
    }

    /**
     * 自定义 ERR_603 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err603($msg)
    {
        self::$_errMsg[self::ERR_603] = $msg;
        return self::ERR_603;
    }

    /**
     * 自定义 ERR_604 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err604($msg)
    {
        self::$_errMsg[self::ERR_604] = $msg;
        return self::ERR_604;
    }

    /**
     * 自定义 ERR_605 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err605($msg)
    {
        self::$_errMsg[self::ERR_605] = $msg;
        return self::ERR_605;
    }

    /**
     * 自定义 ERR_606 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err606($msg)
    {
        self::$_errMsg[self::ERR_606] = $msg;
        return self::ERR_606;
    }

    /**
     * 自定义 ERR_607 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err607($msg)
    {
        self::$_errMsg[self::ERR_607] = $msg;
        return self::ERR_607;
    }

    /**
     * 自定义 ERR_608 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err608($msg)
    {
        self::$_errMsg[self::ERR_608] = $msg;
        return self::ERR_608;
    }

    /**
     * 自定义 ERR_609 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err609($msg)
    {
        self::$_errMsg[self::ERR_609] = $msg;
        return self::ERR_609;
    }

    /**
     * 自定义 ERR_610 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err610($msg)
    {
        self::$_errMsg[self::ERR_610] = $msg;
        return self::ERR_610;
    }

    /**
     * 自定义 ERR_610 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err611($msg)
    {
        self::$_errMsg[self::ERR_611] = $msg;
        return self::ERR_611;
    }

    public static function err612($msg)
    {
        self::$_errMsg[self::ERR_612] = $msg;
        return self::ERR_612;
    }

    public static function customCode($code, $msg)
    {
        self::$_errMsg[$code] = $msg;
        return $code;
    }
}

/* End of file ActionCode.php */