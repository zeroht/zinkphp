<?php
/**
 * Clientapi 错误码
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/23 @thu: 创建；
 */

namespace App\Clientapi;

class ActionCode
{
    /**
     * Action方法默认返回值列表
     */

    const SUCCESS = 200;                           // 成功
    
    // 默认错误码
    const ERR_40000 = 40000;                       // 无效的请求(url不正确)
    const ERR_40001 = 40001;                       // App Token无效
    const ERR_40002 = 40002;                       // 用户未登录
    const ERR_40003 = 40003;                       // 禁止访问
    const ERR_40004 = 40004;                       // 验证码过期
    const ERR_40005 = 40005;                       // 验证码错误
    const ERR_40006 = 40006;                       // 激活信息不正确

    // 请求错误码
    const ERR_50000 = 50000;                       // 请求失败
    const ERR_50001 = 50001;                       // 缺少必要参数
    const ERR_50002 = 50002;                       // 参数格式错误
    const ERR_50003 = 50003;                       // 服务器异常
    
    /**
     * 扩展错误码,区分不同的业务提示
     * 一般App应对不同错误需要有不同逻辑处理时,使用如下错误码, 否则统一使用50000码
     */
    const ERR_60001 = 60001;                       
    const ERR_60002 = 60002;       
    const ERR_60003 = 60003;       
    const ERR_60004 = 60004;       
    const ERR_60005 = 60005;       
    const ERR_60006 = 60006;
    const ERR_60007 = 60007;
    const ERR_60008 = 60008;
    const ERR_60009 = 60009;

    const ERR_60024 = 60024;    //学生课程过期专用
    const ERR_60025 = 60025;    //学生账号过期专用
    const ERR_60026 = 60026;    //当前课程卡已过期
    const ERR_60027 = 60027;    //取消课程卡

    /**
     * 错误码对应的默认信息
     * @var array
     */
    protected static $_errMsg = array(
        self::SUCCESS => '请求成功',
        
        self::ERR_40000 => '无效的请求',
        self::ERR_40001 => '您的软件授权已过期',
        self::ERR_40002 => '您的登录已过期，请重新登录',
        self::ERR_40003 => '网络异常，请稍后再试',
        self::ERR_40004 => '验证码已过期，请重新获取',
        self::ERR_40005 => '验证码错误，请检查',
        self::ERR_40006 => '您的软件未激活',

        self::ERR_50000 => '网络超时，请稍后再试',
        self::ERR_50001 => '缺少必要参数',
        self::ERR_50002 => '提交数据有误',
        self::ERR_50003 => '请求超时，请稍后再试',

        self::ERR_60024 => '当前词库已过期',
        self::ERR_60025 => '当前账号已过期',
        self::ERR_60026 => '当前课程卡已过期',
        self::ERR_60027 => '当前课程已取消'
    );

    /**
     * 返回code对应的message
     * @param $errcode
     * @return string
     */
    public static function getMessage($errcode)
    {
        if (isset(self::$_errMsg[$errcode])) {
            return self::$_errMsg[$errcode];
        } else {
            return self::$_errMsg[self::ERR_50003];
        }
    }

    /**
     * 自定义 ERR_50000 错误消息内容
     * @param null $msg
     * @return int
     */
    public static function err50000($msg = null)
    {
        if (null != $msg) {
            self::$_errMsg[self::ERR_50000] = $msg;
        }

        return self::ERR_50000;
    }

    /**
     * 自定义 ERR_60001 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60001($msg)
    {
        self::$_errMsg[self::ERR_60001] = $msg;
        return self::ERR_60001;
    }

    /**
     * 自定义 ERR_60002 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60002($msg)
    {
        self::$_errMsg[self::ERR_60002] = $msg;
        return self::ERR_60002;
    }

    /**
     * 自定义 ERR_60003 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60003($msg)
    {
        self::$_errMsg[self::ERR_60003] = $msg;
        return self::ERR_60003;
    }

    /**
     * 自定义 ERR_60004 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60004($msg)
    {
        self::$_errMsg[self::ERR_60004] = $msg;
        return self::ERR_60004;
    }

    /**
     * 自定义 ERR_60005 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60005($msg)
    {
        self::$_errMsg[self::ERR_60005] = $msg;
        return self::ERR_60005;
    }

    /**
     * 自定义 ERR_60006 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60006($msg)
    {
        self::$_errMsg[self::ERR_60006] = $msg;
        return self::ERR_60006;
    }

    /**
     * 自定义 ERR_60007 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60007($msg)
    {
        self::$_errMsg[self::ERR_60007] = $msg;
        return self::ERR_60007;
    }

    /**
     * 自定义 ERR_60008 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60008($msg)
    {
        self::$_errMsg[self::ERR_60008] = $msg;
        return self::ERR_60008;
    }

    /**
     * 自定义 ERR_60009 错误消息内容
     * @param $msg
     * @return int
     */
    public static function err60009($msg)
    {
        self::$_errMsg[self::ERR_60009] = $msg;
        return self::ERR_60009;
    }

    public static function isValidCode($code)
    {
        return isset(self::$_errMsg[$code]);
    }

    public static function customCode($code, $msg)
    {
        self::$_errMsg[$code] = $msg;
        return $code;
    }
}
