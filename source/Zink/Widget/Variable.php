<?php
/**
 * Class Variable
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */
namespace Zink\Widget;

class Variable
{
    protected $_val = '';
    
    public function __construct($var)
    {
        $this->_val = $var;
    }
    
    /**
     * 根据PHP各种类型变量生成唯一标识号
     * @param mixed $mix 变量
     * @return string
     */
    public function toGuid()
    {
        if (is_object($this->_val) && function_exists('spl_object_hash')) {
            return spl_object_hash($this->_val);
        } elseif (is_resource($this->_val)) {
            $mix = get_resource_type($this->_val) . strval($this->_val);
        } else {
            $mix = serialize($this->_val);
        }

        return md5($mix);
    }
    
    public function toString(){
        return $this->_val;
    }
}

/* End of file Variable.php */