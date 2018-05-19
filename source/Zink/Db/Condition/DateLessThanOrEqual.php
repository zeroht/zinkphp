<?php
/**
 * Sql Date Condition '<='
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/15 @thu: 创建；
 */

namespace Zink\Db\Condition;


class DateLessThanOrEqual extends SqlCondition
{

    public function __construct($key, $value, $alias = null)
    {
        parent::__construct($key, $value, $alias);
        
        $this->_statement = "{$this->_key} <= {$this->_placeholder}";
        $this->_binds[$this->_placeholder] = "{$value} 23:59:59";
    }
}