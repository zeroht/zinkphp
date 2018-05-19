<?php
/**
 * 公共 拦截器配置 配置文件(pattern_action => interceptor)
 *  pattern_action: action（$_SERVER['SCRIPT_NAME']）的正则匹配表达式(不区分大小写）
 *  eq:
 *      '.*'：                   匹配全部；
 *      '^\/Admin\/.*'：         匹配'/Admin/'开头
 *      '^\/Admin\/login$'：     完全匹配
 * 
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *  1. 2016/5/6 @thu: 创建；
 */

return [
    '.*' => 'ParameterInterceptor',
    '^\/captcha\/image$' => '-LoginInterceptor'
];