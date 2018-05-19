<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */

return [
    '.*' => 'LoginInterceptor,AuthorizationInterceptor,ParameterInterceptor',
    '^\/home\/index' => '-AuthorizationInterceptor,-LoginInterceptor'
];