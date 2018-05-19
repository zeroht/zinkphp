<?php
/**
 * Memcache 缓存类
 * 参考：http://php.net/manual/zh/book.memcached.php
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Zink\Cache;

use Zink\Core\Log;
use Zink\Exception\RuntimeException;

class MemcachedCache extends AbstractCache{

    /**
     * MemcachedCache connector.
     * @param mix $servers 'mem1.domain.com:11211' or [['mem1.domain.com', 11211, 33],['mem2.domain.com', 11211, 67]]
     * @return bool|\Memcached|null
     */
    protected function _connect($servers) {
        if(!extension_loaded("Memcached")){
            throw new RuntimeException('Memcached Extension Not Existed.');
        }

        $memcached = new \Memcached();
        try {
            if (is_array($servers)){
                $ret = $memcached->addServers($servers);
            }else {
                list($host, $port) = explode(":", $servers);
                $ret = $memcached->addServer($host, $port);
            }

            if($ret) {
                return $memcached;
            }else {
                $this->_log->error("Memcached $servers connect failed.");
            }
        }catch (Exception $e) {
            $this->_log->error($e->getMessage());
        }
        
        return FALSE;
    }

    protected function _get($key) {     
        if (!$this->_cacheObj){
            return self::KEY_NOT_FOUND;
        }

        $value = $this->_cacheObj->get($key);
        // @todo 测试
        //$code = $this->_cacheObj->getResultCode(); // 部分机器配置方法无效
        //if ($code != Memcached::RES_SUCCESS){
        if (false === $value){
            // 读取失败或者key不存在
            return self::KEY_NOT_FOUND;
        }
        
        return $value;
    }
    
    /**
     * 设置key的值，
     * @param string $key：redis key 值
     * @param type $value：非string类型会做处理
     * @param int $ttl：过期时间(秒数),不能超过60×60×24×30（30天）;
     * 如果失效的值大于这个值， 服务端会将其作为一个真实的Unix时间戳来处理而不是 自当前时间的偏移
     * @return boolean
     */
    protected function _set($key, $value, $ttl = self::TTL_FOREVER) {
        if (!$this->_cacheObj){
            return FALSE;
        }
        
        $ttl = intval($ttl);
        return $this->_cacheObj->set($key, $value, $ttl);
    }
    
    /**
     * 删除key
     * @param mix $key：单个key，或者多个key的数组
     * @return type 
     */
    protected function _delete($key) {
        if (!$this->_cacheObj){
            return FALSE;
        }
        
        if (is_array($key)){
            return $this->_cacheObj->deleteMulti($key);
        }else {
            return $this->_cacheObj->delete($key);
        }
    }

    public function increment($key, $offset = 1, $ttl = self::TTL_FOREVER) {
        if (!$this->_cacheObj){
            return FALSE;
        }
        
        $value = $this->get($key);
        if (self::KEY_NOT_FOUND === $value){
            return $this->set($key, 1, $ttl) ? 1 : 0;
        }
        
        $offset = intval($offset);
        return $this->_cacheObj->increment ($key, $offset);
    }

    public function decrement($key, $offset = 1, $ttl = self::TTL_FOREVER) {
        if (!$this->_cacheObj){
            return FALSE;
        }
        
        $value = $this->get($key);
        if (self::KEY_NOT_FOUND === $value){
            return 0;
        }
        
        $offset = intval($offset);
        return $this->_cacheObj->decrement($key, $offset);
    }

    // ... 其他方法扩展：http://php.net/manual/zh/book.memcached.php
}

/* End of file MemcachedCache.php */