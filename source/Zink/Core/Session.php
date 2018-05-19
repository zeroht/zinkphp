<?php
/**
 * Session 操作类
 *  参考：http://php.net/manual/zh/memcached.sessions.php
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/9 @thu: 创建；
 */

namespace Zink\Core;

use Common\Constant;
use Zink\Widget\ApacheEnv;
use Zink\Widget\SessionHandler;

class Session
{
    private static $_started = false;

    /**
     * 缓存session值，避免频繁读取memcache
     * @type array
     */
    private static $_cache = array();
    
    public static function getSessionId()
    {
        return self::$_started ? session_id() : '';
    }
    
    public static function setSessionId($sid)
    {
        self::startSession($sid);
    }

    /**
     * 登录之后调用此方法跟换sessionid，防止回话固定攻击
     * 也可是使用token值验证(App登录存储的cid就是Token）
     * @return mixed
     */
    public static function regenerateSessionId()
    {
        session_regenerate_id(true);
        return session_id();
    }

    public static function startSession($sid = null, $expire = null)
    {
        if (self::$_started){
            $sid && session_id($sid);
            return session_id();
        }

        $expire = $expire ? $expire : Constant::get('SESSION_EXPIRE');
        $name = Constant::get('SESSION_NAME');
        ini_set('session.gc_maxlifetime', $expire); // 秒
        session_name($name);

        $handler = new SessionHandler($expire);
        session_set_save_handler($handler, true);

        // 下面这行代码可以防止使用对象作为会话保存管理器时可能引发的非预期行为
        register_shutdown_function('session_write_close');

        $sid = $sid ? $sid : $_COOKIE[$name];
        $path = Constant::get('SESSION_COOKIE_PATH');
        $domain = Constant::get('SESSION_COOKIE_DOMAIN');
        // TODO: CooKie时长尽可能长，确保终端的Cookie保持一致
        session_set_cookie_params(86400 * 30, $path, $domain);
        if (empty($sid)) {
            self::$_started = session_start();
            $sid = session_id();
        } else {
            session_id($sid);
            self::$_started = session_start();
        }

        // set apache env value
        ApacheEnv::setSessionId($sid);
        return $sid;
    }

    /**
     * 获取Session值
     * @param $key
     * @return mixed
     */
    public static function get($key){
        if (!isset(self::$_cache[$key])){
            self::$_cache[$key] = $_SESSION[$key];
        }

        return self::$_cache[$key];
    }

    /**
     * 修改Session值
     * @param $key
     * @param $value
     */
    public static function set($key, $value){
        $_SESSION[$key] = $value;
        self::$_cache[$key] = $value;
    }

    /**
     */
    public static function delete($key){
        unset($_SESSION[$key]);
        unset(self::$_cache[$key]);
    }
    
    /**
     * 销毁session
     * @return boolean
     */
    public static function destroy($sessionid = '_CURRENT_SESSION_ID_')
    {
        // TODO: 定义为'_CURRENT_SESSION_ID_' 防止调用方传入空值把当前session清掉啦
        if ($sessionid === '_CURRENT_SESSION_ID_'){
            // 销毁当前session
            self::$_started = false;
            self::$_cache = array();

            unset($_SESSION);
            return session_destroy();
        } else if ($sessionid){
            // 销毁指定session
            $handler = new SessionHandler();
            return $handler->destroy($sessionid);
        }
    }
}
/* End of file Session.php */