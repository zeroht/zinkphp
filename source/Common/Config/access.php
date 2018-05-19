<?php
/**
 * url访问频度配置（示例）
 *  pattern_action: action（$_SERVER['SCRIPT_NAME']）的正则匹配表达式(不区分大小写）
 *  eq:
 *      '.*'：                   匹配全部；
 *      '^\/Admin\/.*'：         匹配'/Admin/'开头
 *      '^\/Admin\/login$'：     完全匹配
 *  匹配多条规则使用第一条
 * @author:  thu
 * @date:    2016/5/6
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

return [
	//'^/app/active$' => ['type' => 'day', 'period' => 86400, 'count' => 10],
];

/* End of file access.php */