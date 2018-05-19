<?php

/**
 * 数据库操作基类,对库的操作,不限定具体表
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/16 @thu: 创建；
 */
namespace Common\Model;

use Common\Constant;
use Zink\Core\DbSupport;
use Zink\Core\Singleton;
use Zink\Core\SqlStatement;
use Zink\Db\Condition\Equal;
use Zink\Db\Condition\In;
use Zink\Db\Result\Collection;
use Zink\Widget\Pager;

abstract class AbstractSingleTableModel extends Singleton
{
    use DbSupport;

    /**
     * 重写仅仅是为了定义返回值,方便ide自动提示方法
     * @return AbstractSingleTableModel
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Model constructor.
     */
    protected function __construct()
    {
        $this->_initDB();
    }

    /**
     * 添加一条数据
     * @param array $data
     * @return bool
     */
    public function add(array $data)
    {
        return $this->_db->insert($data);
    }

    /**
     * 返回上次插入成功的自增id
     * @return int
     */
    public function insertId()
    {
        return $this->_db->insertId();
    }

    /**
     * 根据条件批量删除记录
     * @param SqlStatement $condition
     * @return bool
     * @throws \Zink\Exception\RuntimeException
     */
    public function delete(SqlStatement $condition)
    {
        return $this->_db->where($condition)->delete();
    }

    /**
     * 根据特定字段值批量删除记录
     * @param $key
     * @param $value
     * @return bool
     * @throws \Zink\Exception\RuntimeException
     */
    public function deleteByKey($key, $value)
    {
        if (is_array($value)){
            $condition = new In($key, $value);
        }else {
            $condition = new Equal($key, $value);
        }
        
        return $this->_db->where($condition)->delete();
    }

    /**
     * 根据id值删除一条记录
     * @param $id
     * @return bool
     */
    public function deleteById($id)
    {
        if (empty($id)){
            return FALSE;
        }
        
        return $this->deleteByKey('id', $id);
    }

    /**
     * 批量修改记录
     * @param $data
     * @param SqlStatement $condition
     * @param bool $successOnlyEffectRows
     * @return bool
     * @throws \Zink\Exception\RuntimeException
     */
    public function update($data, SqlStatement $condition, $successOnlyEffectRows = false, $isRefreshUpdateTime = true)
    {
        return $this->_db->where($condition)->update($data, $successOnlyEffectRows, $isRefreshUpdateTime);
    }

    /**
     * 根据特定字段值批量修改记录
     * @param $data
     * @param $key
     * @param $value
     * @return bool
     */
    public function updateByKey($data, $key, $value)
    {
        if (is_array($value)){
            $condition = new In($key, $value);
        }else {
            $condition = new Equal($key, $value);
        }
        
        return $this->update($data, $condition);
    }

    /**
     * 根据id值修改一条记录
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateById($data, $id)
    {
        if (empty($id)){
            return FALSE;
        }
        
        return $this->updateByKey($data, 'id', $id);
    }

    /**
     * 根据条件查询记录数量
     * @param SqlStatement|null $condition
     * @return int
     */
    public function count(SqlStatement $condition = null)
    {
        $count = $this->_db->where($condition)->count();
        return $count ? $count : 0;
    }

    public function uniqueCount($field, SqlStatement $condition = null)
    {
        return $this->_db->where($condition)->computeField('COUNT', "DISTINCT {$field}");
    }

    /**
     * 根据条件查询记录最大数量
     * @param $field
     * @param SqlStatement|null $condition
     * @return mixed
     */
    public function max($field, SqlStatement $condition = null)
    {
        $max = $this->_db->where($condition)->max($field);
        return $max ? $max : 0;
    }

    /**
     * 根据条件获取某列值的和
     * @param $field
     * @param SqlStatement|null $condition
     * @return mixed
     */
    public function sum($field, SqlStatement $condition = null)
    {
        $sum = $this->_db->where($condition)->sum($field);
        return $sum ? $sum : 0;
    }
    
    public function avg($field, SqlStatement $condition = null)
    {
        return $this->_db->where($condition)->avg($field);
    }

    
    /**
     * 根据条件查询第一条记录
     * @param SqlStatement $condition 查询条件
     * @param array|null $orderby
     * @return null|\Zink\Db\Result\Record
     */
    public function getFirst(SqlStatement $condition, array $orderby = null, $field = '*')
    {
        return $this->_db->where($condition)->orderby($orderby)->field($field)->first();
    }

    /**
     * 根据条件查询第一条记录的列值
     * @param $id
     * @param $field
     * @return null|\Zink\Db\Result\Record
     */
    public function getFirstWithColumnById($id, $field = '*')
    {
        $condition = new Equal('id', $id);
        return $this->_db->where($condition)->field($field)->first();
    }

    /**
     * 根据特定字段值查询第一条记录
     * @param $key
     * @param $value
     * @param array|null $orderby
     * @param $field
     * @return null|\Zink\Db\Result\Record
     */
    public function getFirstByKey($key, $value, array $orderby = null, $field = '*')
    {
        if (is_array($value)){
            $condition = new In($key, $value);
        }else {
            $condition = new Equal($key, $value);
        }

        return $this->getFirst($condition, $orderby, $field);
    }

    /**
     * 根据id值查询一条记录
     * @param $id
     * @param $field
     * @return bool|null|\Zink\Db\Result\Record
     */
    public function getFirstById($id, $field = '*')
    {
        if (empty($id)){
            return FALSE;
        }

        return $this->getFirstByKey('id', $id, null, $field);
    }
    
    /**
     * 查询所有id数据的结果集
     * @param array $idArr
     * @param array|null $orderby
     * @return \Zink\Db\Result\Collection
     */
    public function getByIds(array $idArr, array $orderby = null)
    {
        if (empty($idArr)){
            return FALSE;
        }
        
        return $this->_db->where(new In('id', $idArr))->orderby($orderby)
            ->select();
    }

    /**
     * 根据条件获取部分记录
     *  记录数量有上限，参考MAX_DB_QUERY_COUNT常量
     * @param SqlStatement|null $condition
     * @param int $start
     * @param int $count
     * @param array|null $orderby
     * @param string $field
     * @return bool|null|\Zink\Db\Result\Collection
     */
    public function getList(SqlStatement $condition = null, $start = 0, $count = 20,
                            array $orderby = null, $field = '*')
    {
        $max = Constant::get('MAX_DB_QUERY_COUNT');
        $count = $count > $max ? $max : $count;
        return $this->_db->where($condition)
            ->orderby($orderby)
            ->field($field)
            ->limit($start, $count)
            ->select();
    }

    /**
     * 根据条件获取所有记录
     *  记录数量有上限，参考MAX_DB_QUERY_COUNT常量
     * @param SqlStatement|null $condition Where or SqlCondition
     * @param array|null $orderby
     * @param string $field
     * @return bool|null|\Zink\Db\Result\Collection
     */
    public function getAll(SqlStatement $condition = null, array $orderby = null,
                           $field = '*', $max_count = null)
    {
        $count = ($max_count === null) ? Constant::get('MAX_DB_QUERY_COUNT') : $max_count;
        return $this->_db->where($condition)
            ->orderby($orderby)
            ->field($field)
            ->limit(0, $count)
            ->select();
    }

    /**
     * 获取一列的数据
     * @param $field
     * @param SqlStatement|null $condition
     * @param bool $distinct
     * @return array
     */
    public function columns($field, SqlStatement $condition = null, $distinct = true)
    {
        $column = $this->_db->where($condition)->column($field, $distinct);
        return $column ? $column : [];
    }
    
    
    /**
     * 根据条件获取满足条件的分页记录
     * @param SqlStatement|null $condition
     * @param int $p
     * @param int $pn
     * @param array|null $orderby
     * @param string $field
     * @return Pager
     */
    public function getListInPager(SqlStatement $condition = null, $p = 1, $pn = 20, array $orderby = null,
                                   $field = '*')
    {
        $total = $this->count($condition);
        $pager = new Pager($total, $p, $pn);
        if (!$pager->isValidPage()) {
            return $pager;
        }

        $result = $this->getList($condition, $pager->getStart(), $pn,
            $orderby, $field);
        $pager->setResult($result);

        return $pager;
    }

    /**
     * 根据条件分组查询记录数量
     * @param SqlStatement|null $condition
     * @return Collection
     */
    public function countByGroup(SqlStatement $condition = null, $field = '*', $groupby = null, $max_count = null, $orderby = NULL)
    {
        $count = ($max_count === null) ? Constant::get('MAX_DB_QUERY_COUNT') : $max_count;
        return $this->_db->where($condition)
            ->field($field)
            ->orderby($orderby)
            ->groupby($groupby)
            ->limit(0, $count)
            ->select();
    }
    
}

/* End of file Model.php */