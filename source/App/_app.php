<?php
/**
 * App公共入口文件
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */
//header('X-FRAME-OPTIONS:DENY');
header('X-FRAME-OPTIONS:SAMEORIGIN');

//define('APP_NAME', 'Admin'); //必须与目录同名

define('PROJECT_NAME', 'ZinkPHP');
// 引入实际的入口文件
require dirname(__FILE__) . '/../bootstrap.php';