<?php
/**
 *  日志配置文件
 * pattern参数的格式含义：
 *  %d 输出日志时间点的日期或时间，默认格式为Y-m-d H:i:s
 *  %f 输出日志信息所属的类的文件名
 *  %l 输出日志事件的发生位置，即输出日志信息的语句处于它所在的类的第几行
 *  %m 输出代码中指定的信息，如log(message)中的message
 *  %n 输出一个回车换行符，“\n”
 *  %p 输出优先级，即DEBUG(5)，DETAIL(4), INFO(3)，WARNING(2)，ERROR(1)，FATAL(0)。如果是调用debug()输出的，则为DEBUG，依此类推
 *  %i 输出用户的ip
 *  %s 输出用户的session id
 *  %t 输出当前的毫秒值
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

/**
 * 配置名找不到时，会使用第一个配置，同时file={$name}.run.log
 */
return [
	/* 默认日志配置 */
	'default' => array(
		'file' => 'run.log',                 		// 日志文件名
		'appender' => 'day',						// 文件增长方式：null-单个文件追加；'day|month|year'-按天|月|年增长
        'pattern' => '[%d %t][%p][%s][%i]%m%n',		// 自定义状态,支持特殊字符替换，
        'level' => 5                        		// 日志级别,默认是用common.php里的LOG_LEVEL
	),
	/* mysql日志配置 */
    'db' => array(
		'file' => 'mysql.log',
		'appender' => 'day',
		'pattern' => '[%d %t][%p][%s][%i]%m%n',
		'level' => 5
	),
	/* 访问频度控制日志 */
	'access' => array(
		'file' => 'access.log',
		'appender' => 'day',
		'pattern' => '[%d %t][%p][%s][%i]%m%n',
		'level' => 5
	),
	/* innerapi日志配置 */
    'api' => array(
		'file' => 'innerapi.log',
		'appender' => 'day',
		'pattern' => '[%d %t][%p][%s][%i]%m%n',
		'level' => 5
	)
];

/* End of file log.php */
