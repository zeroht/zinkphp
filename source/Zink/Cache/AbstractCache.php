<?php
/**
 * 数据缓存服务抽象基类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */


namespace Zink\Cache;

use \Zink\Core\Debugger;
use Zink\Core\Log;

abstract class AbstractCache
{

    const KEY_NOT_FOUND = null; // 定义一个null值表示 key 值不存在
    const TTL_FOREVER = 0;   // 永久
    const TTL_MINUTE = 60;
    const TTL_HOUR = 3600;
    const TTL_DAY = 86400;

    protected $_cacheObj = null;
    protected $_log = null;

    private $_dataCache = array();
    protected static $_instances = array();

    /**
     * 创建Cache缓存单例
     * @param string $address：缓存服务器地址
     * @return AbstractCache
     */
    public static function getInstance($address)
    {
        $link = md5($address);
        $class = get_called_class();
        if (!isset(self::$_instances[$link])) {
            $instance = new $class($address);
            self::$_instances[$link] = $instance;
        }

        return self::$_instances[$link];
    }

    public function __construct($server) {
        $this->_cacheObj = $this->_connect($server);
        $this->_log = Log::getLogger();
    }

    abstract protected function _connect($server);

    abstract protected function _get($key);

    abstract protected function _set($key, $value, $ttl);

    abstract protected function _delete($key);

    public function __call($name, $arguments)
    {
        if ($this->_cacheObj && method_exists($this->_cacheObj, $name)){
            return call_user_func_array(array($this->_cacheObj, $name), $arguments);
        }

        return FALSE;
    }

    /**
     * 获取缓存数据
     * @param string $key 数据索引值
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->_dataCache[$key])) {
            $this->_dataCache[$key] = $this->_get($key);
        }

        return $this->_dataCache[$key];
    }

    /**
     * 设置缓存数据
     * @param string $key 数据索引值
     * @param string $value 数据内容
     * @param int $ttl 有效时长（秒），0表示不过期
     * @return bool
     */
    public function set($key, $value, $ttl = self::TTL_FOREVER)
    {
        $ttl = $ttl > 0 ? $ttl : 0;
        if ($this->_set($key, $value, $ttl)) {
            $this->_dataCache[$key] = $value;
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 删除缓存数据
     * @param string $key 数据索引值
     * @return boolean
     */
    public function delete($key)
    {
        if (isset($this->_dataCache[$key])) {
            unset($this->_dataCache[$key]);
        }

        return $this->_delete($key);
    }

    /**
     * 销毁过期缓存
     * @param $maxlifetime 有效时长（秒），0表示不过期
     * @return boolean
     */
    public function gc($maxlifetime)
    {
        return TRUE;
    }

    /**
     * 存贮某个代码片段的返回值到缓存中，下次直接使用
     * @param string $key 数据索引值
     * @param callable $callback 获取数据源函数
     * @param int $ttl 有效时长（秒），0表示不过期
     * @param boolean $refresh 是否强刷缓存
     * @return mixed
     */
    public function store($key, callable $callback, $ttl = self::TTL_FOREVER,
            $refresh = false)
    {
        $value = $this->get($key);
        if (!$refresh && !Debugger::isDevelopMode() && $value !== self::KEY_NOT_FOUND) {
            return $value;
        }

        // 调用callback函数刷新数据
        $value = $callback();
        if ($value !== self::KEY_NOT_FOUND) {
            $this->set($key, $value, $ttl);
        }

        return $value;
    }
    

    /**
     * 将一个数值元素增加参数offset指定的大小
     * @param string $key 数据索引值
     * @param int $offset 增加值
     * @param int $ttl 有效时长（秒），0表示不过期
     * @return int
     */
    public function increment($key, $offset = 1, $ttl = self::TTL_FOREVER)
    {
        $value = $this->get($key);
        if (self::KEY_NOT_FOUND === $value) {
            $value = 0;
        }

        $newValue = intval($value) + intval($offset);
        return $this->set($key, $newValue, $ttl) ? $newValue : 0;
    }

    /**
     * 将一个数值元素减少参数offset指定的大小
     * @param string $key 数据索引值
     * @param int $offset 增加值
     * @param int $ttl 有效时长（秒），0表示不过期
     * @return int
     */
    public function decrement($key, $offset = 1, $ttl = self::TTL_FOREVER)
    {
        $value = $this->get($key);
        if (self::KEY_NOT_FOUND === $value) {
            return 0;
        }

        $value = intval($value) - intval($offset);
        $newValue = $value >= 0 ? $value : 0;
        return $this->set($key, $newValue, $ttl) ? $newValue : 0;
    }

}

/* End of file AbstractCache.php */