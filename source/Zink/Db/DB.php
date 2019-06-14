<?php
/**
 * DB操作类
 *  一个库共享一个link实例,一个表创建一个DB实例
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/14 @thu: 创建；
 */

namespace Zink\Db;
use Zink\Core\SqlStatement;
use Zink\Db\Query\JoinQuery;
use Zink\Db\Result\Collection;
use Zink\Db\Result\Record;
use Zink\Exception\RuntimeException;
use Zink\Widget\Datetime;

class DB
{

    const SORT_TYPE_ASC = 1;
    const SORT_TYPE_DESC = 0;
    
    //通用排序:非通用不要在此处定义,如 shorthand_extra_id 倒序
    const UPDATED_AT_DESC = ['updated_at' => self::SORT_TYPE_DESC];     //更新时间倒序
    const CREATED_AT_DESC = ['created_at' => self::SORT_TYPE_DESC];     //创建时间倒序

    protected $_link = null;      // 当前数据库操作对象
    protected $_dbName = '';
    // 当前表名
    protected $_table = '';     // 数据表名
    protected $_modelName = null;    // 视图模型名
    // 当前SQL指令
    private $_queryFields = '*';
    private $_queryStatement = '';
    private $_queryBinds = null;
    private $_queryGroupby = '';
    private $_queryHaving = '';
    private $_queryOrderby = '';
    private $_queryLimit = '';

    private static $_instances = array();
    private static $_transLink = null;

    /**
     * 创建DB实例
     * @param null $table 表名,默认为null
     * @param null $dbName 数据库名,默认为database配置默认数据库
     * @return DB
     */
    public static function create($table = null, $dbName = null)
    {
        $tb = $dbName ? "{$dbName}.{$table}" : $table;
        if (!isset(self::$_instances[$tb])) {
            self::$_instances[$tb] = new DB($table, $dbName);
        }

        return self::$_instances[$tb];
    }
    
    public function __construct($table = null, $dbName = null)
    {
        $this->_table = $table;
        $this->_dbName = $dbName;

        $this->_link = Factory::createLink($dbName);
        self::$_transLink = $this->_link;
    }

    /**
     * 获取自增性主键表格最后插入的id值 
     */
    public function insertId()
    {
        return $this->_link->insertId();
    }

    /**
     * 需手动处理SQL中的变量，防止SQL注入，
     *  不建议在业务代码中使用，可以在脚本中使用
     * @param type $statement
     * @param null $binds
     * @param type $pdoName
     * @return bool|null|Collection
     */
    public function runSql($statement, $binds = null, $pdoName = null)
    {
        $this->clearQuery(); // 清除已有的条件
        $pdoName = $pdoName ? $pdoName : $this->_table;
        return $this->_link->query($statement, $binds, $pdoName);
    }

    /**
     * 连表查询,只支持2个表连表
     * @param JoinQuery $query
     * @param int $total
     * @return bool|null|Collection
     */
    public function runJoinQuery(JoinQuery $query, &$total = 0)
    {
        $this->where($query->where)
                ->groupby($query->groupby)
                ->orderby($query->orderby)
                ->limit($query->limit);

        $sql = "SELECT SQL_CALC_FOUND_ROWS " . $query->field
                . " FROM " . $query->table
                . $this->_queryStatement
                . $this->_queryGroupby
                //. $this->_queryHaving
                . $this->_queryOrderby
                . $this->_queryLimit;
        
        $result = $this->runSql($sql, $this->_queryBinds);
        
        $rows = $this->runSql("SELECT FOUND_ROWS() AS cnt");
        $row = $rows->first();
        $total = $row ? $row->get('cnt') : 0;
        return $result;
    }

    public function getFields($table = null)
    {
        $table = $table ? $table : $this->_table;
        return $this->_link->getFields($table);    
    }
    
    /**
     * 设置查询字段
     * @param mixed $fields 字段,多字段用','分割或数组填充
     * @param bool $exclude 是否查询$fields之外多字段
     * @return $this
     */
    public function field($fields = '*', $exclude = false)
    {
        if (is_string($fields) && false !== strpos($fields, ',')) {
            $fields = explode(',', $fields);
        }

        if (is_array($fields)) {
            $query_fields = array();
            $all_fields = $this->_link->getFields($this->_table);

            if ($exclude){
                foreach ($all_fields as $field => $info) {
                    if (in_array($field, $fields)) {
                        continue;
                    }

                    $query_fields[] = self::parseKey($field);
                }
            }else {
                foreach ($fields as $field){
                    /*if (!isset($all_fields[$field])){
                        continue;
                    }*/

                    $query_fields[] = self::parseKey($field);
                }
            }

            $this->_queryFields = implode(',', $query_fields);
        }else {
            $this->_queryFields = $fields;
        }

        return $this;
    }

    /**
     * 指定查询条件 支持安全过滤
     * @param SqlStatement|null $condition
     * @return $this
     */
    public function where(SqlStatement $condition = null)
    {
        $this->_queryStatement = '';
        $this->_queryBinds = null;
        if (null === $condition){
            return $this;
        }

        list($whereStr, $binds) = $condition->toQuery();
        if ($whereStr){
            $this->_queryStatement = ' WHERE '.$whereStr;
            $this->_queryBinds = $binds;
        }

        return $this;
    }

    /**
     * group by 语句
     * @param string $group
     * @return $this
     */
    public function groupby($group)
    {
        $this->_queryGroupby = $group ? ' GROUP BY ' . $group : '';
        return $this;
    }

    /**
     * @param $having
     * @return $this
     */
    public function having($having)
    {
        $this->_queryHaving = $having ? ' HAVING ' . $having : '';
        return $this;
    }
    
    /**
     * @param array|null $orderbys
     * @return $this
     */
    public function orderby(array $orderbys = null)
    {
        $orderby = self::orderbysToString($orderbys);
        if ($orderby){
            $this->_queryOrderby = ' ORDER BY ' . $orderby;
        }

        return $this;
    }

    /**
     * @param int $start
     * @param string $cnt
     * @return $this
     */
    public function limit($start = 0, $cnt = null)
    {
        $start = intval($start);
        if ($cnt === null) {
            $this->_queryLimit = $start ? ' LIMIT ' . $start : '';
        } else {
            $cnt = intval($cnt);
            $this->_queryLimit = ' LIMIT ' . $start . ',' . $cnt;
        }

        return $this;
    }

    public function clearQuery()
    {
        $this->_queryFields = '*';
        $this->_queryStatement = '';
        $this->_queryBinds = null;
        $this->_queryGroupby = '';
        $this->_queryHaving = '';
        $this->_queryOrderby = '';
        $this->_queryLimit = '';
    }

    /**
     * @return bool|null|Collection
     */
    public function select()
    {
        $sql = "SELECT " . $this->_queryFields
                . " FROM " . $this->_table
                . $this->_queryStatement
                . $this->_queryGroupby
                . $this->_queryHaving
                . $this->_queryOrderby
                . $this->_queryLimit;

        return $this->runSql($sql, $this->_queryBinds, $this->_table);
    }

    /**
     * 取第一条记录
     * @return Record|null
     */
    public function first()
    {
        $result = $this->limit(0, 1)->select();
        if ($result instanceof Collection) {
            return $result->first();
        }

        return null;
    }

    /**
     * 获取某一列的数据，存为数组
     * @param $field
     * @param bool $distinct
     * @return array|null
     */
    public function column($field, $distinct = true)
    {
        if ($this->_queryFields == '*') {
            $this->_queryFields = $field;
        }

        $result = $this->select();
        if ($result instanceof Collection) {
            return $result->column($field, $distinct);
        }

        return NULL;
    }

    /**
     * 对某一列进行简单的数学公式计算
     * @param $expression
     * @param $field
     * @return int
     */
    public function computeField($expression, $field)
    {
        $field = self::parseKey($field);
        $result = $this->field("{$expression}({$field}) AS result")->first();

        if (is_subclass_of($result, Record::class)) {
            return $result->get('result');
        }

        return 0;
    }

    /**
     * 获取SQL查询的记录数
     * @return int
     */
    public function count()
    {
        return $this->computeField('COUNT', '1');
    }

    /**
     * 求某一列的平均值
     * @param $field
     * @return float
     */
    public function avg($field)
    {
        return $this->computeField('AVG', $field);
    }

    /**
     * 求某一列的和
     * @param $field
     * @return float
     */
    public function sum($field)
    {
        return $this->computeField('SUM', $field);
    }

    /**
     * 求某一列的最小值
     * @param $field
     * @return float
     */
    public function min($field)
    {
        return $this->computeField('MIN', $field);
    }

    /**
     * 求某一列的最大值
     * @param $field
     * @return float
     */
    public function max($field)
    {
        return $this->computeField('MAX', $field);
    }

    /**
     * 插入记录
     * @param array $data
     * @return bool
     */
    public function insert(array $data)
    {
        $keys = $values = $binds = array();
        $fields = $this->_link->getFields($this->_table);
        foreach ($data as $key => $val) {
            if (!isset($fields[$key])) {
                continue;
            }
            
            $placeholder = Driver::keyToPlaceholder($key);
            $keys[] = self::parseKey($key);
            $values[] = $placeholder;
            $binds[$placeholder] = $val;
        }

        if (empty($keys)) {
            return FALSE;
        }

        $sql = 'INSERT INTO ' . $this->_table
                . ' (' . implode(',', $keys) . ') VALUES ('
                . implode(',', $values) . ')';
        return $this->runSql($sql, $binds);
    }

    /**
     * 更新记录
     * @param $data
     * @param bool $successOnlyEffectRows
     * @return bool
     * @throws RuntimeException
     */
    public function update($data, $successOnlyEffectRows = false, $isRefreshUpdateTime = true)
    {
        $update = array();
        $binds = is_array($this->_queryBinds) ? $this->_queryBinds : array();
        if (is_array($data)) {
            $fields = $this->_link->getFields($this->_table);
            if (!isset($data['updated_at']) && $isRefreshUpdateTime) {
                $data['updated_at'] = Datetime::now();
            }
            foreach ($data as $key => $val) {
                if (!isset($fields[$key])) {
                    continue;
                }

                $placeholder = Driver::keyToPlaceholder($key);
                $key = self::parseKey($key);
                
                $update[] = "{$key} = {$placeholder}";
                $binds[$placeholder] = $val;
            }
        }

        if (empty($this->_queryStatement) || empty($update)) {
            // 禁止无条件的修改，防止程序的编码失误导致数据全被修改；
            throw new RuntimeException('sql update must with conditions.');
        }

        $sql = 'UPDATE ' . $this->_table . ' SET ' . implode(',', $update)
                . $this->_queryStatement
                . $this->_queryOrderby
                . $this->_queryLimit;

        $ret = $this->runSql($sql, $binds);
        if ($ret && $successOnlyEffectRows){
            return $this->effectRowCount() > 0 ? TRUE : FALSE;
        }

        return $ret;
    }

    public function effectRowCount()
    {
        return $this->_link->effectRowCount();
    }
    
    /**
     * @param $field
     * @param int $number
     * @return bool
     * @throws RuntimeException
     */
    public function increase($field, $number = 1)
    {
        $number = intval($number);
        if (0 >= $number) {
            return TRUE;
        }

        if (empty($this->_queryStatement)) {
            // 禁止无条件的修改，防止程序的编码失误导致数据全被修改；
            throw new RuntimeException('sql increase must with conditions.');
        }

        $field = self::parseKey($field);
        $sql = "UPDATE " . $this->_table . " SET $field = $field + $number"
                . $this->_queryStatement
                . $this->_queryOrderby
                . $this->_queryLimit;
        $binds = $this->_queryBinds;

        return $this->runSql($sql, $binds);
    }

    /**
     * @param $field
     * @param int $number
     * @return bool
     * @throws RuntimeException
     */
    public function decrease($field, $number = 1)
    {
        $number = intval($number);
        if (0 >= $number) {
            return TRUE;
        }

        if (empty($this->_queryStatement)) {
            // 禁止无条件的修改，防止程序的编码失误导致数据全被修改；
            throw new RuntimeException('sql decrease must with conditions.');
        }

        $field = self::parseKey($field);
        $sql = "UPDATE " . $this->_table . " SET $field = $field - $number"
                . $this->_queryStatement
                . $this->_queryOrderby
                . $this->_queryLimit;
        $binds = $this->_queryBinds;

        return $this->runSql($sql, $binds);
    }

    /**
     * 删除记录
     * @return bool
     * @throws RuntimeException
     */
    public function delete()
    {
        if (empty($this->_queryStatement)) {
            // 禁止无条件的删除，防止程序的编码失误导致数据全被删除
            throw new RuntimeException('sql delete must with conditions.');
        }

        $sql = 'DELETE FROM ' . $this->_table
                . $this->_queryStatement
                . $this->_queryOrderby
                . $this->_queryLimit;
        $binds = $this->_queryBinds;

        return $this->runSql($sql, $binds);
    }

    /**
     * 字段和表名处理
     * @param string $key
     * @return string
     */
    public static function parseKey($key)
    {
        if (!self::$_transLink){
            self::$_transLink = Factory::createLink();
        }

        return self::$_transLink->parseKey($key);
    }

    /**
     * 转义字符串,防止SQL注入
     * @param $string
     * @return string
     */
    public static function escape($string)
    {
        return Driver::escape($string);
    }

    /**
     * 转义like通配符，不同引擎通配符不一样
     * @param type $string
     */
    public static function escapeLike($string)
    {
        self::$_transLink = Factory::createLink($dbName);
        return self::$_transLink->escapeLike($string);
    }

    /**
     * orderbys 处理
     * @param array|null $orderbys
     * @param null $tableAlias
     * @return null
     */
    public static function orderbysToString(array $orderbys = null, $tableAlias = null)
    {
        if (is_array($orderbys) && count($orderbys) > 0){
            $orderList = [];
            foreach ($orderbys as $by => $isAsc){
                if (!self::isValidField($by)){
                    // TODO: 过滤非法，防SQL注入
                    continue;
                }

                $by = $tableAlias ? $tableAlias.'.'.$by : $by;
                $sort = $isAsc ? 'ASC' : 'DESC';
                $orderList[] = self::parseKey($by).' '.$sort;
            }

            return implode(',', $orderList);
        }    
        
        return null;
    }
    
    /**
     * 启动事务
     * @param null $dbName
     * @return bool
     * @throws RuntimeException
     */
    public static function transBegin($dbName = null)
    {
        self::$_transLink = Factory::createLink($dbName);

        return self::$_transLink->transBegin();
    }

    /**
     * 提交事务
     * @return boolean
     */
    public static function transCommit()
    {
        return self::$_transLink ? self::$_transLink->transCommit() : FALSE;
    }

    /**
     * 回滚事务
     * @return bool
     */
    public static function transRollback()
    {
        return self::$_transLink ? self::$_transLink->transRollback() : FALSE;
    }

    /**
     * 是否非法字段名
     * name,nick_name,name2,`order`, student.name, ...
     * @param $field
     * @return false|int
     */
    public static function isValidField($field)
    {
        return preg_match('/^[A-Za-z0-9\._`]+$/', $field);
    }

}

/* End of file DB.php */
