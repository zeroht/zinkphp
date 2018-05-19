<?php
/**
 * Class ArrayList
 * 索引数组对象,只支持[]访问key,不支持->访问
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Widget;
use ArrayAccess;

/**
 * Class ArrayList
 * 索引数组对象,只支持[]访问key,不支持->访问
 * @package Zink\Core
 */
class ArrayList extends IteratorSupport implements \ArrayAccess
{
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
        return isset($this->_arrData[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
        return $this->_arrData[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
        $this->_arrData[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
        unset($this->_arrData[$offset]);
    }

    /**
     * 添加一个元素
     * @param $value
     */
    public function push($value)
    {
        $this->_arrData[] = $value;
    }

    public function merge($array)
    {
        $this->_arrData = array_merge($this->_arrData, $array);
    }

    public function join($glue, $unique = false)
    {
        $arr = $unique ? array_unique($this->_arrData) : $this->_arrData;
        return implode($glue, $arr);
    }

    public function contains($string)
    {
        return in_array($string, $this->_arrData);
    }

    public function unique($rebuildIndex = false)
    {
        $data = array_unique($this->_arrData);
        return $rebuildIndex ? array_values($data) : $data;
    }

    public static function safeGet($array, $key, $default = "")
    {
        if ($array && isset($array[$key])){
            return $array[$key];
        }

        return $default;
    }

    public static function removeEmptyStringValueKeys($array)
    {
        $newArray = [];
        foreach ($array as $key => $value){
            if (is_string($value) && '' === trim($value)){
                continue;
            }

            $newArray[$key] = $value;
        }

        return $newArray;
    }
}

/* End of file ArrayList.php */
