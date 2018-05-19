<?php
/**
 * Sql Condition Base Class
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/15 @thu: 创建；
 */

namespace Zink\Db\Condition;

use Zink\Core\SqlStatement;
use Zink\Db\DB;
use Zink\Db\Driver;

abstract class SqlCondition implements SqlStatement
{
    const CDN_EQ = '=';
    const CDN_NEQ = '!=';
    const CDN_GT = '>';
    const CDN_GTE = '>=';
    const CDN_LT = '<';
    const CDN_LTE = '<=';
    const CDN_LK = 'LIKE';
    const CDN_NLK = 'NOT LIKE';
    const CDN_IN = 'IN';
    const CDN_NIN = 'NOT IN';
        
    protected $_key = '';
    /**
     * 保存原始输入值,子类不可修改,用于判断 isEmptyValue
     * @var string
     */
    private $_value = '';
    protected $_placeholder = '';
    protected $_statement = '';
    protected $_binds = [];

    /**
     * SqlCondition constructor.
     * @param $key
     * @param $value
     * @param null $alias
     * @param null $cdn
     */
    public function __construct($key, $value, $alias = null, $cdn = null)
    {
        $key = $alias ? "{$alias}.{$key}" : $key;
        $placeholder = Driver::keyToPlaceholder($key);
        
        $this->_key = DB::parseKey($key);
        $this->_value = $value;
        $this->_placeholder = $placeholder;
        
        if ($cdn){
            $this->_statement = "{$this->_key} {$cdn} {$this->_placeholder}";
        }

        $this->_binds[$placeholder] = $value;
    }
    
    /**
     * @return bool
     */
    public function isEmptyValue()
    {
        return ($this->_value === '' || $this->_value === null);
    }

    /**
     * @return array [$statement, $binds]
     */
    public function toQuery()
    {
        return [$this->_statement, $this->_binds];
    }
}

/* End of file DBWhere.class.php */