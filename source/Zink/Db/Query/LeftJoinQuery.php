<?php
/**
 * Left Join 连表查询,目前只支持2个表的连表查询,
 * 不建议使用超过2表的查询
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/16 @thu: 创建；
 */


namespace Zink\Db\Query;

use Zink\Db\Condition\FieldEqual;
use Zink\Db\Where\Where;

class LeftJoinQuery extends JoinQuery
{
    /**
     * JoinQuery constructor.
     * @param string  $table 本表表名或orm类名
     * @param string $otherTable 关联表表名或orm类名
     * @param string  $localKey 本表外健,默认为 '关联表名_id'
     * @param string  $foreignKey 关联表外健,默认为'id'
     */
    public function __construct($table, $otherTable, $localKey = null, $foreignKey = null)
    {
        $this->_initJoin($table, $otherTable, $localKey, $foreignKey, self::LEFT_JOIN);
    }
}

/* End of file LeftJoinQuery.php */