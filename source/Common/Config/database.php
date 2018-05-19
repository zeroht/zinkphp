<?php
/**
 * 通用数据库配置
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *  1. 2016/5/6 @thu: 创建；
 */

return [
    /* 默认的数据库配置 */
    'driver' => 'mysql',                // [必选] 数据库驱动
    'dsn' => '',
    'db_host' => '{db_host}',           // [必选] 主机名
    'db_user' => '{db_user}',           // [必选] 用户名
    'db_password' => '{db_password}',   // [必选] 密码
    'db_name' => '{db_name}',           // [必选] 数据库名
    'db_port' => 3306,                  // [可选] 端口，默认为3306
    'db_charset' => 'utf8mb4',             // [可选] 设置字符集
    'db_namespace' => '',               // [可选] 默认为空
    'log_name' => 'db',                 // [必选] 日志配置名
    'connector' => array(
        /* 支持多个数据库连接 (不考虑不同数据库同名情况，设计时应避免同名）
        'test' => array(
            'db_host' => '{db_test_host}',
            'db_user' => '{db_test_user}',
            'db_password' => '{db_test_password}',
            'db_port' => '3306',
            'db_namespace' => 'test'
        )
        */
    )
];

/* End of file database.php */
