<?php
/**
 *
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/26 @thu: 创建；
 

/**
 * App公共入口文件
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

define('APP_NAME', 'Test'); //必须与目录同名
define('APP_PATH', dirname(__FILE__).'/');

define('APP_ROOT', APP_PATH);

// 引入实际的入口文件
require APP_PATH . '/../bootstrap.php';
