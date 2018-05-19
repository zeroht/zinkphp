<?php
/**
 * 数据记录集合类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/16 @thu: 创建；
 */

namespace Zink\Db\Result;

use Zink\Core\SqlStatement;
use Zink\Db\Condition\In;
use Zink\Db\DB;
use Zink\Db\Where\WhereAnd;
use Zink\Widget\ArrayObject;
use Zink\Widget\IteratorSupport;


class Collection extends IteratorSupport
{
    private $_dbName = null;
    private $_tableName = null;

    /**
     * Collection constructor.
     * @param string $dbName 数据库名
     * @param string $tableName 数据表名
     */
    public function __construct($dbName = null, $tableName = null)
    {
        $this->_dbName = $dbName;
        $this->_tableName = $tableName;
        parent::__construct(null);
    }

    /**
     * 添加一条记录路
     * @param Record $record
     */
    public function push(Record $record)
    {
        $this->_arrData[] = $record;
    }


    /**
     * 取某一列记录值
     * @param $field 字段名
     * @param bool $distinct 是否去重
     * @return array
     */
    public function column($field, $distinct = true)
    {
        $colArray = array();
        foreach ($this->_arrData as $record){
            $colArray[] = $record->get($field);
        }

        return $distinct ? array_unique($colArray) : $colArray;
    }

    /**
     * @param $index_key key值对应多记录field名
     * @param null $column_key value值对应多记录field名,null 返回Record记录
     * @param bool $toArray 是否返回纯数组
     * @return array|ArrayObject
     */
    public function toMap($index_key, $column_key = null, $toArray = false)
    {
        $map = new ArrayObject();
        $data = parent::toArray();
        foreach ($data as $record) {
            $newData[] = $record;
            $value = $column_key ? $record[$column_key] : $record;

            $map->set($record[$index_key], $value);
        }

        return $toArray ? $map->toArray() : $map;
    }

    /**
     * @param $index_key key值对应多记录field名
     * @param null $column_key value值对应多记录field名,null 返回Record记录
     * @param bool $toArray 是否返回纯数组
     * @return array|ArrayObject
     */
    public function toMapList($index_key, $column_key = null, $toArray = false)
    {
        $list = [];
        $data = parent::toArray();
        foreach ($data as $record) {
            $newData[] = $record;
            $value = $column_key ? $record[$column_key] : $record;
            if ($toArray && $value instanceof IteratorSupport){
                $value = $value->toArray();
            }

            $list[$record[$index_key]][] = $value;
        }

        return $toArray ? $list : new ArrayObject($list);
    }

    /**
     * 连表查询,查询结果以对象形式保存在"join_{$table}"字段中
     * @param $table 关联表名
     * @param string $otherFields 连表查询字段
     * @param  string  $foreignKey 本表外健,默认为 '关联表名_id'
     * @param  string  $otherKey 关联表外健,默认为'id'
     * @param bool $inObject 是否将结果聚集到object中
     * @return $this
     */
    public function joinOne($table, $otherFields = '*', $foreignKey = null,
                            $otherKey = null, $inObject = true, $otherCondition = null)
    {
        if ($this->isEmpty()){
            return $this;
        }

        $foreignKey = $foreignKey ? $foreignKey : "{$table}_id";
        $otherKey = $otherKey ? $otherKey : 'id';

        $foreignValues = $this->column($foreignKey, true);
        $db = DB::create($table, $this->_dbName);

        $groupby = null;
        if (preg_match('/(SUM|COUNT|AVG)\([^\)]+\)/i', $otherFields)){
            $groupby = $otherKey;
        }

        if ($otherCondition instanceof SqlStatement){
            $condition = new WhereAnd([
                $otherCondition,
                new In($otherKey, $foreignValues)
            ]);
        }else {
            $condition = new In($otherKey, $foreignValues);
        }

        $collection = $db->field($otherFields)->where($condition)
            ->groupby($groupby)->select();
        if ($collection instanceof Collection){
            $otherMap = $collection->toMap($otherKey);
            $joinKey = 'join_'.$table;
            foreach ($this->_arrData as &$record){
                $otherValue = $record->get($foreignKey);
                $joinRecord = $otherMap->get($otherValue);
                if ($inObject){
                    $record->set($joinKey, $joinRecord);
                } else {
                    foreach ($joinRecord as $key => $value){
                        if ($key == $otherKey){
                            continue;
                        }

                        $joinKey = $table.'_'.$key;
                        $record->set($joinKey, $value);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * 连表查询,查询结果以数组形式保存在"join_{$table}"字段中
     * 应用场景:主表的$foreignKey是关联表名的主键id集合(逗号分割)
     * @param $table 关联表名 (M:N)
     * @param string $otherFields 连表查询字段
     * @param  string  $foreignKey 本表外健,默认为 '关联表名_ids'
     * @param  string  $otherKey 关联表外健,默认为'id'
     * @param bool $onlyOneColumn 是否只获取链表的一个字段放入一维数组
     * @return $this
     */
    public function joinMany($table, $otherFields = '*', $foreignKey = null,
                             $otherKey = null, $onlyOneColumn = false)
    {
        if ($this->isEmpty()){
            return $this;
        }

        $foreignKey = $foreignKey ? $foreignKey : "{$table}_ids";
        $otherKey = $otherKey ? $otherKey : 'id';

        $colArray = array();
        foreach ($this->_arrData as $record){
            $tmp = explode(',', $record->get($foreignKey));
            $colArray = array_merge($tmp, $colArray);
        }
        $foreignValues = array_unique($colArray);
        $db = DB::create($table, $this->_dbName);

        $fields = $onlyOneColumn ? [$otherKey, $otherFields] : $otherFields;

        $collection = $db->where(new In($otherKey, $foreignValues))->field($fields)->select();
        $joinKey = 'join_'.$table;

        if ($collection instanceof Collection){
            $column_key = $onlyOneColumn ? $otherFields : null;
            $otherMap = $collection->toMap($otherKey, $column_key, true);
            foreach ($this->_arrData as &$record) {
                $record->set($joinKey, []);

                $tmpArr = [];
                $foreignValue = $record->get($foreignKey);
                if (empty($foreignValue)){
                    continue;
                }

                $otherValues = explode(',', $foreignValue);
                foreach ($otherValues as $val) {
                    if (!isset($otherMap[$val])){
                        continue;
                    }

                    $tmpArr[] = $otherMap[$val];
                }

                $record->set($joinKey, $tmpArr);
            }
        }

        return $this;
    }

    /**
     * 连表查询,查询结果以数组形式保存在"join_{$table}"字段中
     * 应用场景:关联表名通过"{$table}_id"关联主表
     * @param string $table 关联表名(1:N)
     * @param string $otherFields 连表查询字段
     * @param  string  $otherKey 关联表外健,默认为'id'
     * @param  array  $orderby 排序方式
     * @param int $limit 记录数
     * @return $this
     */
    public function joinList($table, $otherFields = '*', $otherKey = null, $orderby = null, $limit = 100)
    {
        if ($this->isEmpty()){
            return $this;
        }

        $primaryKey = "id";
        $otherKey = $otherKey ? $otherKey :  "{$this->_tableName}_{$primaryKey}";
        $orderby = $orderby ? $orderby : [$primaryKey => DB::SORT_TYPE_DESC];

        $foreignValues = [];
        foreach ($this->_arrData as $record){
            $foreignValues[] = $record->get($primaryKey);
        }

        $db = DB::create($table, $this->_dbName);
        $collection = $db->where(new In($otherKey, $foreignValues))
            ->field($otherFields)
            ->orderby($orderby)
            ->limit(0, $limit)
            ->select();
        $joinKey = 'join_'.$table;
        $record->set($joinKey, []);

        if ($collection instanceof Collection){
            $otherMap = $collection->toMapList($otherKey, null, true);
            foreach ($this->_arrData as &$record) {
                $foreignValue = $record->get($primaryKey);
                $tmpArr = isset($otherMap[$foreignValue]) ? $otherMap[$foreignValue] : [];
                $record->set($joinKey, $tmpArr);
            }
        }

        return $this;
    }

    /**
     * 计算聚合属性值
     * @param $table
     * @param $compute
     * @param null $foreignKey
     * @param null $otherKey
     * @param null $condition
     * @return $this
     * @throws \Zink\Exception\UnexpectetException
     */
    public function joinComputeFields($table, $compute, $foreignKey = null, $otherKey = null, $condition = null)
    {
        if ($this->isEmpty()){
            return $this;
        }

        $foreignKey = $foreignKey ? $foreignKey : 'id';
        $otherKey = $otherKey ? $otherKey : "{$this->_tableName}_id";

        $queryField = [$otherKey];
        $joinKey = [];
        foreach ($compute as  $field => $type){
            $type = strtolower($type);
            if (!in_array($type, ["count", "sum", "avg", "min", "max"])){
                continue;
            }

            $cKey = "{$table}_{$type}_{$field}";
            $queryField[] = "{$type}({$field}) AS {$cKey}";
            $joinKey[$cKey] = $type;
        }

        if (empty($joinKey)){
            return $this;
        }

        $foreignValues = $this->column($foreignKey, true);
        $db = DB::create($table, $this->_dbName);

        $myCondition = new WhereAnd([
            new In($otherKey, $foreignValues)
        ]);

        if ($condition){
            $myCondition->andCondition($condition);
        }

        $collection = $db->where($myCondition)
            ->field($queryField)->groupby($otherKey)->select();
        if ($collection instanceof Collection){
            $otherMap = $collection->toMap($otherKey, null, true);
            foreach ($this->_arrData as &$record) {
                $foreignValue = $record->get($foreignKey);
                $otherValue = $otherMap[$foreignValue];
                foreach ($joinKey as $jk => $t){
                    $record->set($jk, $otherValue[$jk]);
                }
            }
        } else {
            foreach ($this->_arrData as &$record) {
                foreach ($joinKey as $jk => $t){
                    $record->set($jk, 0);
                }
            }
        }

        return $this;
    }

    public function joinCount($table, $foreignKey = null, $otherKey = null, $condition = null){
        return $this->joinComputeFields($table, ["id" => "count"], $foreignKey, $otherKey, $condition);
    }

    public static function tableToJoinKey($table)
    {
        return 'join_'.$table;
    }

    /**
     * @param array $index_key 数组key值对应多记录field名
     * @param null $column_key value值对应多记录field名,null 返回Record记录
     * @param bool $toArray 是否返回纯数组
     * @return array|ArrayObject
     */
    public function toMapByJoinKeys(array $index_key, $column_key = null, $toArray = false)
    {
        $map = new ArrayObject();
        $data = parent::toArray();
        foreach ($data as $record) {
            $newData[] = $record;
            $value = $column_key ?$record[$column_key] : $record;
            $index_values = [];
            foreach ($index_key as $index_value) {
                $index_values[] = $record[$index_value];
            }
            $index_string = implode('_', $index_values);
            $map->set($index_string, $value);
        }
        return $toArray ? $map->toArray() : $map;
    }

}

/* End of file Collection.php */