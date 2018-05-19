<?php
/**
 *  通用常量配置文件
 *  1.通过 \Common\Constant::get('xxx');访问
 *  2.{xxx}变量值从.env文件读取
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *  1. 2016/5/6 @thu: 创建；
 */

return [
    /* 调试 */
    'DEBUG_XHPROF_ENABLED' => false,          // 是否启用xhprof性能追踪
    'DEBUG_REQUEST_ENABLED' => false,         // 是否记录客户端请求

    /* 通用配置 */
    'ERROR_PAGE' => '/',                      // 错误页面
    'TPL_FILE_SUFFIX' => '.html',             // 默认模板后缀
    'TPL_JSON_SUFFIX' => '.tpl',
    'TPL_FILE_DEFAULT' => 'index',            // 默认模板名
    'MAX_DB_QUERY_COUNT' => 20000,            // 数据库一次查询的最大记录数
    'PAGER_PN' => 20,                         // 分页查询时每页默认记录条数

    /* session设置 */
    'SESSION_NAME' => 'ZSID',
    'SESSION_EXPIRE' => 86400,          // 秒
    'SESSION_KEY_PREFIX' => 'sid.'. PROJECT_NAME . '.' . APP_NAME.'.',
    'SESSION_KEY_USER' => 'user',       // 登录后保存用户信息的key
    'SESSION_COOKIE_DOMAIN' => '',
    'SESSION_COOKIE_PATH' => '/',

    /* access filter */
    'ACCESS_FILTER_PERIOD' => 60,       // 计数周期（秒）
    'ACCESS_FILTER_VISITS' => 300,      // 允许最大次数（含）
    'ACCESS_FILTER_FORBID' => 3600,     // 禁止访问时长（秒）

    /* memcached */
    'MEMCACHE_ENABLED' => true,
    'MEMCACHE_SERVERS' => '127.0.0.1:11211',
    'MEMCACHE_OCS_ENABLED' => false,                 // 是否使用OCS作为memcached
    'MEMCACHE_OCS_SERVER' => '{ocs_server}',
    'MEMCACHE_OCS_ID' => '{ocs_access_id}',
    'MEMCACHE_OCS_PASSWORD' => '{ocs_access_password}',

    /* redis */
    'REDIS_ENABLED' => true,
    'REDIS_SERVERS' => '{redis_servers}',

    /* 文件缓存配置 */
    'FILE_CACHE_PATH' => ROOT_PATH.'/_zcache/',

    /* 日志相关配置信息 */
    'LOG_LEVEL' => '5',
    'LOG_SAVE_PATH' => ROOT_PATH.'/_zlogs/',
    'LOG_NAME_API' => 'api',
    'LOG_NAME_PAY' => 'pay'
];

/* End of file constant.php */
