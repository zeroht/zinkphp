<?php
/**
 * redis 扩展类
 * 参考：https://github.com/phpredis/phpredis#setex-psetex
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/7 @thu: 创建；
 */

namespace Zink\Cache;

use Zink\Exception\RuntimeException;

class RedisCache extends AbstractCache
{
    private $_dbindex = 0;
    const TIMEOUT = 5;

    const DB_DEFAULT = 0;

    const DB_MEMCACHE_REPLACE = 8;
    const DB_SESSION = 9;

    public static function getInstance($address, $dbindex = self::DB_DEFAULT)
    {
        $link = md5($address.'_'.$dbindex);
        if (!isset(self::$_instances[$link])) {
            $instance = new RedisCache($address, $dbindex);
            self::$_instances[$link] = $instance;
        }

        return self::$_instances[$link];
    }

    public function __construct($server, $dbindex = self::DB_DEFAULT) {
        $this->_dbindex = $dbindex;

        parent::__construct($server);
    }

    protected function _connect($server)
    {
        if (!extension_loaded("redis")) {
            throw new RuntimeException('Redis Extension Not Existed.');
        }
        $redis = new \Redis();
        list($host, $port, $auth) = explode(":", $server);
        try {
            if ($redis->connect($host, $port, self::TIMEOUT) && $redis->auth($auth)
                && $redis->select($this->_dbindex)) {
                // 默认不做序列化
                $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
                return $redis;
            } else {
                $this->_log->error("Redis $server connect failed.");
            }
        } catch (Exception $e) {
            $this->_log->error($e->getMessage());
        }

        return FALSE;
    }

    protected function _get($key)
    {
        if (!$this->_cacheObj){
            return self::KEY_NOT_FOUND;
        }

        /* 不用  null, false 作为返回值是因为redis的key值可能存储 null或false */
        return $this->_cacheObj->get($key);
    }

    protected function _set($key, $value, $ttl = self::TTL_FOREVER)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        $ttl = intval($ttl);
        return $this->_cacheObj->set($key, $value, $ttl);
    }
    
    protected function _delete($key)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        return $this->_cacheObj->delete($key);
    }

    public function ttl($key)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        return $this->_cacheObj->ttl($key);
    }

    public function incr($key)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        return $this->_cacheObj->incr($key);
    }

    public function incrBy($key, $offset)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        return $this->_cacheObj->incrBy($key, $offset);
    }

    public function increment($key, $offset = 1, $ttl = self::TTL_FOREVER)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        if (!$this->_cacheObj->exists($key)) {
            $this->_cacheObj->set($key, 0, $ttl);
        }

        $value = $this->_cacheObj->incrBy($key, $offset);
        return $value > 0 ? $value : 0;
    }

    public function decrement($key, $offset = 1, $ttl = self::TTL_FOREVER)
    {
        if (!$this->_cacheObj){
            return FALSE;
        }

        $value = $this->_cacheObj->decrBy($key, $offset);

        return $value > 0 ? $value : 0;
    }

    

    // ... 其他方法扩展：https://github.com/phpredis/phpredis
}

/* End of file RedisCache.php */