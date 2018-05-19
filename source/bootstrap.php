<?php

/**
 * Zink启动文件
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */
/* 需要php 5.6.0 以上的支持 */
version_compare(PHP_VERSION,'5.6.0','>')  or  exit('require PHP >= 5.6.0 !');
defined('PROJECT_NAME') or exit('No Project Existed.');
defined('APP_NAME') or exit('No App Existed.');
defined('APP_KEY') or define('APP_KEY', 'Zink');

define('Zink_Version', 'v2.0.0');

ini_set('display_errors', 'Off');
error_reporting(E_ERROR);

define('Z_ENVIRONMENT','develop');

defined('Z_JS_VERSION') or define('Z_JS_VERSION', time());
/**
 * 约定：
 * 1、所有目录的常量定义均以'/'结尾；
 */
define('ROOT_PATH', dirname(__FILE__).'/');
define('LIB_PATH', ROOT_PATH.'Lib/');
define('COMMON_PATH', ROOT_PATH.'Common/');
define('ZINK_PATH', ROOT_PATH.'Zink/');
define('CACHE_PATH', ROOT_PATH.'_zcache/');
defined('LOG_PATH') or define('LOG_PATH', ROOT_PATH.'_zlogs/');
define('SCRIPT_PATH', ROOT_PATH.'_zscript/');

defined('APP_ROOT') or define('APP_ROOT', ROOT_PATH.'App/');
defined('APP_PATH') or define('APP_PATH', APP_ROOT.APP_NAME.'/');


// 设置系统时区
date_default_timezone_set('PRC');

// 打开缓冲区，缓存一些不必要的输出
ob_start();

// 引入Zink的基础定义库
require_once ZINK_PATH . 'Core/Zink.php';

// 注册AUTOLOAD方法
spl_autoload_register(array('\Zink\Core\Autoloader', 'autoload'));

// 加载常量配置
\Common\Constant::load(APP_NAME);

\Zink\Core\Debugger::traceTime(\Zink\Core\Debugger::TAG_BEGIN);
\Zink\Core\Debugger::traceMemory(\Zink\Core\Debugger::TAG_BEGIN);

/* End of file bootstrap.php */
