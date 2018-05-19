<?php

/**
 * 数据库操作基类,对库的操作,不限定具体表
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/16 @thu: 创建；
 */
namespace Zink\Core;

use Common\Constant;
use Zink\Db\Condition\Equal;
use Zink\Db\Condition\In;
use Zink\Db\DB;
use Zink\Db\Query\JoinQuery;
use Zink\Widget\Pager;

class Model extends Singleton
{
    use DbSupport;

    /**
     * 重写仅仅是为了定义返回值,方便ide自动提示方法
     * @return Model
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
     * Sql查询
     * @param $statement
     * @param null $binds
     * @param null $pdoName
     * @return mixed
     */
    public function runSql($statement, $binds = null, $pdoName = null)
    {
        return $this->_db->runSql($statement, $binds, $pdoName);
    }
    
    /**
     * 连表查询获取满足条件的部分记录
     * @param JoinQuery $query
     * @param int $start
     * @param int $count
     * @param int $total
     * @return bool|null|\Zink\Db\Collection
     */
    public function getListByJoinQuery(JoinQuery $query, $start = 0, $count = 20, &$total = 0){
        $max = Constant::get('MAX_DB_QUERY_COUNT');
        $count = $count > $max ? $max : $count;
        $query->limit($start, $count);

        return $this->_db->runJoinQuery($query, $total);
    }

    /**
     * 连表查询获取满足条件的分页记录
     * @param JoinQuery $query
     * @param int $p
     * @param int $pn
     * @return Pager
     */
    public function getListByJoinQueryInPager(JoinQuery $query, $p = 1, $pn = 20)
    {
        $start = ($p - 1) * $pn;
        $total = 0;
        $result = $this->getListByJoinQuery($query, $start, $pn, $total);
        $pager = new Pager($total, $p, $pn);
        $pager->setResult($result);
        return $pager;
    }

    /**
     * 连表查询获取全部记录
     *
     * @param JoinQuery $query
     * @param $total
     * @return bool|null|\Zink\Db\Result\Collection
     */
    public function getAllByJoinQuery(JoinQuery $query, &$total = 0){
        $max = Constant::get('MAX_DB_QUERY_COUNT');
        $query->limit(0, $max);

        return $this->_db->runJoinQuery($query, $total);
    }
}

/* End of file Model.php */