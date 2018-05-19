<?php
/**
 * Session 处理类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/9 @thu: 创建；
 */

namespace Zink\Widget;

use Common\Constant;
use Zink\Cache\RedisCache;
use Zink\Core\Cache;
class SessionHandler implements \SessionHandlerInterface
{

    private $_cache = null;
    private $_expire = 0;       // 0 永久有效

    public function __construct($expire = 0)
    {
        //$this->_cache = Cache::getMemcached();
        $this->_cache = Cache::getRedis(RedisCache::DB_SESSION);
        $this->_expire = $expire;
    }

    private function _id2key($session_id)
    {
        return Constant::get('SESSION_KEY_PREFIX').$session_id;
    }

    function open($save_path , $name)
    {
        return TRUE;
    }

    function close()
    {
        return TRUE;
    }

    function read($session_id)
    {
        $key = $this->_id2key($session_id);
        return $this->_cache->get($key);
    }

    function write($session_id, $session_data )
    {
        $key = $this->_id2key($session_id);
        return $this->_cache->set($key, $session_data, $this->_expire);
    }

    function destroy($session_id)
    {
        $key = $this->_id2key($session_id);
        return $this->_cache->delete($key);
    }

    function gc($maxlifetime)
    {
        return $this->_cache->gc($maxlifetime);
    }

}
/* End of file SessionHandler.php */