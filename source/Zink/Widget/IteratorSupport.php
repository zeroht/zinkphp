<?php
/**
 * 迭代器类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Widget;

class IteratorSupport implements \Iterator
{
    protected $_arrData = array();

    public function __construct($data = null)
    {
        if(is_array($data)){
            //$this->_arrData = $data; // 子类可能提前赋值
            foreach($data as $key => $value){
                $this->_arrData[$key] = $value;
            }
        }
    }

    public function first()
    {
        $keys = array_keys($this->_arrData);
        return $this->_arrData[$keys[0]];
    }

    public function last()
    {
        $keys = array_keys($this->_arrData);
        return $this->_arrData[array_pop($keys)];
    }

    public function count()
    {
        return count($this->_arrData);
    }

    public function isEmpty()
    {
        return empty($this->_arrData);
    }

    /* (non-PHPdoc)
     * @see Iterator::current()
     */

    public function current()
    {
        return current($this->_arrData);
    }

    /* (non-PHPdoc)
     * @see Iterator::key()
     */

    public function key()
    {
        return key($this->_arrData);
    }

    /* (non-PHPdoc)
     * @see Iterator::next()
     */

    public function next()
    {
        return next($this->_arrData);
    }

    /* (non-PHPdoc)
     * @see Iterator::rewind()
     */

    public function rewind()
    {
        reset($this->_arrData);
    }

    /* (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {

        return key($this->_arrData) !== null;
    }

    /**
     * 返回array数组
     * @param array|null $filter 指定返回的key,其他的过滤掉
     * @return array
     */
    public function toArray(array $filter = null)
    {
        $newData = array();
        foreach ($this->_arrData as $key => $record) {
            if ($record instanceof IteratorSupport){
                $newData[$key] = $record->toArray($filter);
            }else if (!$filter || in_array($key, $filter)) {
                $newData[$key] = $record;
            }
        }

        return $newData;
    }
}

/* End of file IteratorSupport.php */
