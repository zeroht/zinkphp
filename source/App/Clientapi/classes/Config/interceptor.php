<?php
/**
 * 公共 interceptor 配置文件。
 * action（$_SERVER['SCRIPT_NAME']）匹配规则
 * 从第一条开始正则匹配
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

return [
    '.*' => 'ParameterInterceptor,LoginInterceptor',
    //'^\/my\/(login)$' => '-LoginInterceptor'
];
