<?php
/**
 * 缓存构造器类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Core;
use Common\Constant;
use Zink\Cache\RedisCache;
use Zink\Cache\MemcachedCache;
use Zink\Cache\OcsCache;
use Zink\Cache\FileCache;
class Cache
{
    /**
     * 创建redis实例
     * @return \Zink\Cache\AbstractCache
     */
    public static function getRedis($dbindex = RedisCache::DB_DEFAULT)
    {
        if (Constant::get('REDIS_ENABLED')){
            return RedisCache::getInstance(Constant::get('REDIS_SERVERS'), $dbindex);
        }else {
            return self::getFile();
        }
    }

    /**
     * 创建memcached实例
     * @return \Zink\Cache\AbstractCache
     */
    public static function getMemcached()
    {
        if (Constant::get('MEMCACHE_OCS_ENABLED')){
            return OcsCache::getInstance(Constant::get('MEMCACHE_OCS_SERVER'));
        }else if (Constant::get('MEMCACHE_ENABLED')){
            return MemcachedCache::getInstance(Constant::get('MEMCACHE_SERVERS'));
        }else {
            return self::getFile();
        }
    }

    /**
     * 创建filecache实例
     * @return \Zink\Cache\FileCache
     */
    public static function getFile()
    {
        return FileCache::getInstance(Constant::get('FILE_CACHE_PATH'));
    }
}
/* End of file Cache.php */