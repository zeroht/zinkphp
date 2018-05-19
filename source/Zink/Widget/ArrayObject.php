<?php
/**
 * Class ArrayObject
 *  键值数组对象,只支持 ->访问key,不支持[]访问
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Widget;

/**
 * Class ArrayObject
 * 键值数组对象,只支持 ->访问key,不支持[]访问
 * @package Zink\Core
 */
class ArrayObject extends IteratorSupport
{
    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function get($key, $default = null)
    {
        return isset($this->_arrData[$key]) ? $this->_arrData[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->_arrData[$key] = $value;
    }
    
    public function isExisted($key)
    {
        return isset($this->_arrData[$key]);
    }
}
/* End of file ArrayObject.php */