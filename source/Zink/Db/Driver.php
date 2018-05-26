<?php
/**
 * 数据库操作抽象类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/12 @thu: 创建；
 */


namespace Zink\Db;

use Zink\Core\Cache;
use Zink\Core\Debugger;
use Zink\Core\Log;
use Zink\Db\Result\Collection;
use PDO;
use Zink\Widget\Json;

abstract class Driver
{

    protected $_config = null;
    protected $_logger = null;

    // 当前连接ID
    protected $_pdoLink = null;

    // 当前连接的数据库
    protected $_dbName = '';
    protected $_dbNameSpace = '';

    // 事务相关状态
    protected $_transTimes = 0;

    protected $_pdoStatement = null;
    protected $_lastSql = '';
    protected $_effectRowCount = 0;

    // PDO连接参数
    protected $options = [
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false
    ];
    
    private static $_keyCounter = [];

    public function __construct($cfg)
    {
        if (!extension_loaded('PDO')) {
            throw new RuntimeException("Extension Pdo Not Existed!");
        }

        $this->_config = $cfg;
        $this->_logger = Log::getLogger($cfg['log_name']);
        $this->_dbName = $cfg['db_name'];
        $this->_dbNameSpace = $cfg['db_namespace'];
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // 关闭连接
        $this->close();
    }

    /**
     * 创建连接
     */
    protected function _connect()
    {
        if ($this->_pdoLink) {
            return $this->_pdoLink;
        }

        $config = $this->_config;

        $dsn = $config['dsn'];
        $user = $config['db_user'];
        $password = $config['db_password'];

        $startTime = microtime(TRUE);
        try {
            if (empty($dsn)) {
                $dsn = $this->_parseDsn();
            }

            if (version_compare(PHP_VERSION, '5.3.6', '<=')) {
                // 禁用模拟预处理语句
                $this->options[PDO::ATTR_EMULATE_PREPARES] = false;
            }

            $this->_pdoLink = new PDO($dsn, $user, $password, $this->options);
        } catch (\PDOException $e) {
            $time = microtime(TRUE) - $startTime;

            $errcode = $e->getCode();
            $errmsg = $e->getMessage();
            $this->_logger->error("[{$dsn}] Connect {$time}s Failed({$errcode}):{$errmsg}");
            return FALSE;
        }

        return $this->_pdoLink;
    }

    /**
     * 解析pdo连接的dsn信息
     * @return string
     */
    protected function _parseDsn(){
        return '';
    }

    /**
     * 字段和表名处理
     * @param string $key
     * @return string
     */
    protected function _parseKey($key)
    {
        return trim($key);
    }

    public function free()
    {
        if ($this->_pdoStatement){
            $this->_pdoStatement = null;
        }

        $this->_effectRowCount = 0;

    }

    /**
     * 关闭连接
     */
    public function close()
    {
        $this->free();

        $this->_pdoLink = null;
    }

    /**
     * 获取表字段信息
     * @param type $table
     */
    abstract protected function _getFields($table);

    /**
     * 执行预处理查询操作
     * @param $sqlstate
     * @param null $binds
     * @return array|bool
     */
    protected function _prepareQuery($sqlstate, $binds = null)
    {
        $sqlstate = trim($sqlstate);

        $this->free();
        $this->_pdoStatement = $this->_pdoLink->prepare($sqlstate);
        if (!$this->_pdoStatement instanceof \PDOStatement) {
            $this->_logger->error("Pdo prepare filed: {$sqlstate}");
            return FALSE;
        }

        $sql = $sqlstate;
        if (is_array($binds)) {
            $startTime = microtime(TRUE);

            foreach ($binds as $key => $value) {
                /*
                if (is_int($value)) {
                    $data_type = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $data_type = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $data_type = PDO::PARAM_NULL;
                } else {//if(is_string($value)) {
                    $data_type = PDO::PARAM_STR;
                }
                */
                if (is_bool($value)) {
                    $value = $value ? 1 : 0;
                } elseif (is_null($value)) {
                    $value = '';
                }
                
                $data_type = PDO::PARAM_STR;

                if (!Debugger::isOnlineMode()) {
                    // TODO:当SQL比较长（如大数组IN语句）时，非常耗时间，线上不执行
                    $eValue = self::escape($value);
                    $sql = preg_replace("/{$key}([ ,\\)])/", '\'' . $eValue . '\'${1}', $sql);
                    $sql = preg_replace("/ {$key}$/", " '" . $eValue . "' ", $sql);
                }

                $this->_pdoStatement->bindValue($key, $value, $data_type);
            }

            $time = round(microtime(TRUE) - $startTime,4);
            if ($time > 1) {
                $this->_logger->warning("[SQL bindValue Times = {$time}]: $sql");
            }
        }

        $this->_lastSql = $sql;

        try {
            $startTime = microtime(TRUE);

            if (!$this->_pdoStatement->execute()){
                $this->error();
                return FALSE;
            }

            $this->_effectRowCount = $this->_pdoStatement->rowCount();
            $time = round(microtime(TRUE) - $startTime,4);
            if ($time > 1) {
                $this->_logger->error("[SQL Run Times = {$time}]: $sql");
            } else if (!Debugger::isOnlineMode()){
                $this->_logger->info("[SQL({$time})]: $sql");
            }

            if (preg_match('/^(SELECT|SHOW|DESCRIBE|EXPLAIN)[ ]+/i', $sqlstate)) {
                // 查询类语句: SELECT, SHOW, DESCRIBE或 EXPLAIN
                return $this->_pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
            }else {
                return TRUE;
            }
        } catch (\PDOException $e) {
            $this->error();
            return FALSE;
        }
    }

    /**
     * 插入后的递增字段id
     */
    public function insertId()
    {
        if ($this->_pdoLink){
            return $this->_pdoLink->lastInsertId();
        }

        return 0;
    }

    /**
     * 字段和表名处理
     * @param string $key
     * @return string
     */
    public function parseKey($key)
    {
        return trim($key);
    }

    /**
     * 转义like通配符，不同引擎通配符不一样，
     * MySQL        :   _%
     * PostgreSQL   :   _%
     * Oracle       :   _%＿％ (含全角）
     * MS SQL Server:   _%[
     * IBM DB2      :   _%＿％ (含全角）
     * @param type $string
     */
    public function escapeLike($string)
    {
        return addcslashes($string, '%_');
    }
    
    /**
     * 获取字段信息
     * @param $table
     * @return array
     */
    public function getFields($table)
    {
        $func = function () use ($table) {
            return $this->_getFields($table);
        };

        $dbName = $this->_config['db_name'];
        $dbDriver = $this->_config['driver'];
        $cache = Cache::getFile();
        $key = "{$dbDriver}.{$dbName}.{$table}.fields";
        return $cache->store($key, $func);
    }

    /**
     * 执行SQL语句
     * 成功时：查询类语句返回数据结果集，无数据返回NULL；
     *      其他语句返回影响的数据条数；
     * @param $sqlstate
     * @param null $binds
     * @param null $pdoName
     * @return bool|null|Collection
     */
    public function query($sqlstate, $binds = null, $pdoName = null)
    {
        if (!$this->_connect()){
            return FALSE;
        }
        $result = $this->_prepareQuery($sqlstate, $binds);
        if ($result === TRUE) {     // 执行成功
            // 其他查询语句： INSERT, UPDATE, REPLACE 或 DELETE 
            return TRUE;
        } else if ($result === FALSE) {     // 执行失败
            return FALSE;
        } else if (is_array($result) && count($result) > 0) {        // 有查询结果
            // 查询语句：SELECT, SHOW, DESCRIBE 或 EXPLAIN
            $collection = new Collection($this->_dbName, $pdoName);
            foreach($result as $data){
                $record = Factory::createRecord($data, $pdoName, $this->_dbNameSpace);
                $collection->push($record);
            }

            //返回数据集
            return $collection;
        }

        // 无查询结果
        return NULL;
    }
    
    public function effectRowCount()
    {
        return $this->_effectRowCount;
    }

    /**
     * 启动事务
     */
    public function transBegin()
    {
        if (!$this->_connect()){
            return FALSE;
        }

        if ($this->_transTimes == 0) {
            $this->_transTimes = $this->_pdoLink->beginTransaction() ? 1 : 0;
        }else {
            $this->_transTimes++;
        }

        return $this->_transTimes;
    }

    /**
     * 执行事务
     */
    public function transCommit()
    {
        if ($this->_transTimes == 1) {
            $this->_transTimes = 0;
            $result = $this->_pdoLink->commit();
            if (!$result){
                $this->error();
                return FALSE;
            }
        }else {
            $this->_transTimes--;
        }

        return TRUE;
    }

    /**
     * 执行事务
     */
    public function transRollback()
    {
        if ($this->_transTimes > 0) {
            $this->_transTimes = 0;
            $result = $this->_pdoLink->rollBack();
            if (!$result){
                $this->error();
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error()
    {
        $msg = '';
        if ($this->_pdoStatement) {
            $error       = $this->_pdoStatement->errorInfo();
            $msg = $error[1] . ':' . $error[2];
        }

        if ('' != $this->_lastSql) {
            $msg .= "\n [ SQL语句 ] : " . $this->_lastSql;
        }

        $this->_logger->error($msg);
    }

    /**
     * 过滤非法词
     * @param type $string
     */
    public static function escape($string)
    {
        return addslashes($string);
    }

    /**
     * 获取字段的占位符
     * @param $key
     * @return string
     */
    public static function keyToPlaceholder($key)
    {
        $placeholder = ':'.str_replace('.', '_', $key);
        if (isset(self::$_keyCounter[$key])){
            $placeholder .= '_'.self::$_keyCounter[$key];
            self::$_keyCounter[$key]++;
        }else {
            self::$_keyCounter[$key] = 1;
        }

        return $placeholder;
    }
    
    public static function clearPlaceholderCounter()
    {
        self::$_keyCounter = [];
    }
}

/* End of file DBDriver.class.php */