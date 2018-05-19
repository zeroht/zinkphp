<?php
/**
 * 文件缓存类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Zink\Cache;

use Zink\Exception\RuntimeException;

class FileCache extends AbstractCache
{

    private $_filepath = "";

    public function __call($name, $arguments)
    {
        return FALSE;
    }


    protected function _connect($filepath)
    {
        if (!is_dir($filepath)){
            throw new RuntimeException("$filepath Not Existed", 404);
        }

        $this->_filepath = $filepath;
        return $this->_filepath;
    }

    private function _getFileName($key)
    {
        $filename = $this->_filepath . '/' . $key . '.dat';
        $filename = iconv("utf-8", "gb2312", $filename);   //解决中文乱码问题
        return $filename;
    }

    protected function _get($key)
    {
        $filename = $this->_getFileName($key);
        if (is_file($filename)) {
            $value = file_get_contents($filename);
            return unserialize($value); // 反序列化
        }

        return self::KEY_NOT_FOUND;
    }

    protected function _set($key, $value, $ttl = self::TTL_FOREVER)
    {
        $filename = $this->_getFileName($key);
        $value = serialize($value); // 序列化处理
        return file_put_contents($filename, $value);
    }

    protected function _delete($key)
    {
        $keyList = is_array($key) ? $key : array($key);
        foreach ($keyList as $k){
            $filename = $this->_getFileName($k);
            @unlink($filename);
        }
        
        return TRUE;
    }
    
    public function gc($maxlifetime)
    {
        $files = $this->_filepath . '/*.dat';
        foreach (glob($files) as $file) {
            if (is_file($file) && filemtime($file) + $maxlifetime < time()) {
                unlink($file);
            }
        }

        return TRUE;
    }

}

/* End of file FileCache.php */