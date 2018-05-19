<?php
/**
 * 普通连表查询(内连),目前只支持2个表的连表查询,
 * 不建议使用超过2表的查询
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/16 @thu: 创建；
 */


namespace Zink\Db\Query;

use Zink\Db\Condition\FieldEqual;
use Zink\Db\DB;
use Zink\Db\Where\Where;

class JoinQuery
{
    const TABLE_ALIAS = 'A';        // 本表别名
    const OTHER_TABLE_ALIAS = 'B';  // 连表别名
    
    const INNER_JOIN = 'INNER JOIN';
    const LEFT_JOIN = 'LEFT JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    
    public $field = '*';
    public $table = '';
    public $where = null;
    public $groupby = '';
    //public $having = '';
    public $orderby = [];
    public $limit = '';

    /**
     * JoinQuery constructor.
     * @param string  $table 本表表名或orm类名
     * @param string $otherTable 关联表表名或orm类名
     * @param string  $localKey 本表外健,默认为 '关联表名_id'
     * @param string  $foreignKey 关联表外健,默认为'id'
     */
    public function __construct($table, $otherTable, $localKey = null, $foreignKey = null)
    {
        $this->_initJoin($table, $otherTable, $localKey, $foreignKey, self::INNER_JOIN);
    }

    protected function _initJoin($table, $otherTable, $localKey = null, $foreignKey = null, $joinType = self::INNER_JOIN)
    {
        if ($pos1 = strrpos($table, '\\')){
            $table = substr($table, $pos1 + 1);
        }

        if ($pos1 = strrpos($otherTable, '\\')){
            $otherTable = substr($otherTable, $pos1 + 1);
        }

        if ($joinType == self::INNER_JOIN) {
            $this->table = "{$table} AS " . self::TABLE_ALIAS . ",{$otherTable} AS " . self::OTHER_TABLE_ALIAS;

            $localKey = $localKey ? $localKey : 'id';
            $foreignKey = $foreignKey ? $foreignKey : "{$table}_id";

            $this->where = new Where(new FieldEqual($localKey, $foreignKey, [self::TABLE_ALIAS, self::OTHER_TABLE_ALIAS]));
        } else {
            $localKey = $localKey ? $localKey : 'id';
            $foreignKey = $foreignKey ? $foreignKey : "{$table}_id";
            $onCondition = new FieldEqual($localKey, $foreignKey, [self::TABLE_ALIAS, self::OTHER_TABLE_ALIAS]);

            $query = $onCondition->toQuery();
            $this->table = "{$table} AS ".self::TABLE_ALIAS." {$joinType} {$otherTable} AS ".self::OTHER_TABLE_ALIAS
                ." ON ".$query[0];

            // 创建一个空的条件
            $this->where = new Where();
        }
    }
    
    /**
     * Join Field
     * @param array $fields 本表字段
     * @param array $otherFields 关联表字段
     * @return $this
     */
    public function field(array $fields = ['*'], array $otherFields = ['*'])
    {
        $joinFields = array();
        foreach ($fields as $field){
            $joinFields[] = self::TABLE_ALIAS.'.'.$field;
        }

        foreach ($otherFields as $field){
            $joinFields[] = self::OTHER_TABLE_ALIAS.'.'.$field;
        }
        
        $this->field = implode(',', $joinFields);
        return $this;
    }

    /**
     * Join Where
     * @param Where $where 查询条件
     * @return $this
     */
    public function where(Where $where)
    {
        return $this->andWhere($where);
    }

    /**
     * Join And Where
     * @param Where $where 查询条件
     * @return $this
     */
    public function andWhere(Where $where)
    {
        $this->where->andWhere($where);
        return $this;
    }

    /**
     * Join Or Where
     * @param Where $where 查询条件
     * @return $this
     */
    public function orWhere(Where $where)
    {
        $this->where->orWhere($where);
        return $this;
    }

    /**
     * Join Groupby
     * @param null $groupby 本表groupby
     * @param null $otherGroupby 关联表groupby
     * @return $this
     */
    public function groupby($groupby = null, $otherGroupby = null)
    {
        $groupby = $groupby ? self::TABLE_ALIAS.'.'.$groupby : '';
        $otherGroupby = $otherGroupby ? self::OTHER_TABLE_ALIAS.'.'.$otherGroupby : '';

        $this->groupby = $groupby;
        $this->groupby .= ($groupby && $otherGroupby) ? ",{$otherGroupby}" : $otherGroupby;
        return $this;
    }

    /**
     *连表查询不建议使用having语句
    public function having()
    {
        $this->having = '';
        return $this;
    }
    */

    /**
     * Join Orderby
     * @param null $orderby 本表orderby
     * @param null $otherOrderby 关联表orderby
     * @return $this
     */
    public function orderby(array $orderbys = [], array $otherOrderbys = [])
    {
        $orders = [];
        foreach ($orderbys as $key => $value){
            $newKey = self::TABLE_ALIAS.'.'.$key;
            $orders[$newKey] = $value;
        }

        foreach ($otherOrderbys as $key => $value){
            $newKey = self::OTHER_TABLE_ALIAS.'.'.$key;
            $orders[$newKey] = $value;
        }
        
        $this->orderby = $orders;//array_merge($orderbys, $otherOrderbys);
        return $this;
    }

    /**
     * @param int $start
     * @param string $cnt
     * @return $this
     */
    public function limit($start = 0, $cnt = '')
    {
        if (empty($cnt)) {
            $this->limit = $start ? $start : '';
        } else {
            $start = intval($start);
            $cnt = intval($cnt);
            $this->limit = $start . ',' . $cnt;
        }

        return $this;
    }
}

/* End of file JoinQuery.php */