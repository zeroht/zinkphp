<?php
/**
 *  Clientapi 应用入口文件
 *  apache rewrite 配置:
 *      RewriteRule ^/\w+[\w\/\-\.]*\/\w+$  /index.php [QSA,PT,L]
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

define('APP_NAME', 'Clientapi');

/* 引入实际的入口文件 */
require dirname(__FILE__) . '/../../_app.php';
\App\Clientapi\ClientapiApp::start();

