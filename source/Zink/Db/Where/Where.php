<?php
/**
 * Sql Conditions
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/15 @thu: 创建；
 */

namespace Zink\Db\Where;

use Zink\Core\SqlStatement;
use Zink\Db\Condition\SqlCondition;

class Where implements SqlStatement
{
    const LINK_AND = 'AND';
    const LINK_OR = 'OR';

    private $_ignoreEmpty = true;
    private $_appends = array();
    private $_binds = array();

    /**
     * Where constructor.
     * @param SqlCondition|null $condition
     * @param bool $ignoreEmpty
     */
    public function __construct($condition = null, $ignoreEmpty = true)
    {
        $this->_ignoreEmpty = $ignoreEmpty;
        if ($condition instanceof SqlCondition) {
            $this->_appendCondition($condition, '');
        } else if ($condition instanceof Where) {
            $this->_appendWhere($condition, '');
        }
    }

    protected function _appendCondition(SqlCondition $condition, $link)
    {
        if (!$this->_ignoreEmpty || !$condition->isEmptyValue()) {
            list($statement, $binds) = $condition->toQuery();
            $this->_appends[] = [$link => $statement];
            if (is_array($binds)){
                $this->_binds += $binds;
            }
        }

        return $this;
    }

    public function isEmpty()
    {
        return count($this->_appends) > 0 ? false : true;
    }

    /**
     * @param SqlCondition $condition
     * @return $this
     */
    public function andCondition(SqlCondition $condition)
    {
        return $this->_appendCondition($condition, self::LINK_AND);
    }

    /**
     * @param SqlCondition $condition
     * @return $this
     */
    public function orCondition(SqlCondition $condition)
    {
        return $this->_appendCondition($condition, self::LINK_OR);
    }

    protected function _appendWhere(Where $where, $link)
    {
        if ($where->isEmpty()){
            return $this;
        }

        list($statement, $binds) = $where->toQuery();
        if ($where->hasMultiConditions()){
            $sql = '('.$statement.')';
        }else {
            $sql = $statement;
        }

        $this->_appends[] = [$link => $sql];
        if (is_array($binds)){
            $this->_binds += $binds;
        }

        return $this;
    }
    
    /**
     * @param Where $where
     * @return $this
     */
    public function andWhere(Where $where)
    {
        return $this->_appendWhere($where, self::LINK_AND);
    }

    /**
     * @param Where $where
     * @return $this
     */
    public function orWhere(Where $where)
    {
        return $this->_appendWhere($where, self::LINK_OR);
    }

    /**
     * @return mixed
     */
    public function hasMultiConditions()
    {
        return count($this->_appends) > 1 ? true : false;
    }

    /**
     * @return array
     */
    public function toQuery()
    {
        // TODO: Implement toString() method.
        $statements = array();
        $i = 0;

        foreach ($this->_appends as $where) {
            $link = key($where);
            $statement = current($where);
            if ($i++ == 0){
                $statements[] = $statement;
            }else {
                $statements[] = "{$link} {$statement}";
            }
        }

        $sqlstate = implode(' ', $statements);
        return [$sqlstate, $this->_binds];
    }
}

/* End of file WhereAnd.php */