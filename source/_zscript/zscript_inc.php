<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/6 @thu: 创建；
 */

if (function_exists('apache_setenv')){
	// 禁止url访问
	echo "File Not Existing";
	die();
}

define("APP_NAME", "_Script");
define('PROJECT_NAME', 'Script');
/* 代码根目录 */
define("APP_PATH", dirname(__FILE__).'/');
define("ROOT_PATH", APP_PATH.'../');
define('LOG_SAVE_PATH',  APP_PATH.'logs/');

define('LOG_PATH', ROOT_PATH.'_zscript/logs/');

require ROOT_PATH.'bootstrap.php';
ob_end_flush();
/* End of file zscript.php */
