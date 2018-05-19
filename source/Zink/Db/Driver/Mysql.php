<?php
/**
 * Pdo Mysql 实现类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/6/15 @thu: 创建；
 */



namespace Zink\Db\Driver;


use Zink\Db\Driver;
use PDO;
use Zink\Db\Result\Collection;
use Zink\Exception\RuntimeException;

class Mysql extends Driver
{

    public function __construct($cfg)
    {
        if (!extension_loaded('pdo_mysql')) {
            throw new RuntimeException("Extension pdo_mysql Not Existed!");
        }

        parent::__construct($cfg);
    }

    /**
     * 解析pdo连接的dsn信息
     * @return string
     */
    protected function _parseDsn()
    {
        $config = $this->_config;
        $host = $config['db_host'];
        $dbname = $config['db_name'];
        $port = isset($config['db_port']) ? $config['db_port'] : 3306;

        $dsn = "mysql:dbname={$dbname};host={$host};port={$port}";
        if (!empty($config['db_socket'])) {
            $dsn .= ';unix_socket=' . $config['db_socket'];
        }

        if (!empty($config['db_charset'])) {
            //为兼容各版本PHP,用两种方式设置编码
            $charset = $config['db_charset'];
            $this->options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $charset;
            $dsn .= ';charset=' . $charset;
        }

        return $dsn;
    }
    
    protected function _getFields($table)
    {
        $result = $this->query('SHOW COLUMNS FROM `' . $table . '`');
        if ($result instanceof Collection) {
            $result = $result->toArray();
            $info = array();
            $attrCase = $this->_pdoLink->getAttribute(\PDO::ATTR_CASE);

            foreach ($result as $val) {
                if (\PDO::CASE_LOWER != $attrCase) {
                    $val = array_change_key_case($val, CASE_LOWER);
                }

                $info[$val['field']] = array(
                    'name'    => $val['field'],
                    'type'    => $val['type'],
                    'notnull' => (bool) ('' === $val['null']), // not null is empty, null is yes
                    'default' => $val['default'],
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'auto_increment' => (strtolower($val['extra']) == 'auto_increment'),
                );
            }

            return $info;
        }

        return FALSE;
    }

    /**
     * 字段和表名处理
     * @param string $key
     * @return string
     */
    public function parseKey($key)
    {
        $key = trim($key);
        if (!is_numeric($key) && !preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
            $key = '`' . $key . '`';
        }

        return $key;
    }
}

/* End of file Mysql.php */