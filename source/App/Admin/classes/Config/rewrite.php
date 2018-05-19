<?php
/**
 * 公共路由配置
 * 默认的url形式：http://domain.xxx.com/{path}/{action}?{$params}
 * 举例:
 * http://domain.xxx.com/user/login?name=test&pass=1&authcode=3
 * http://domain.xxx.com/user/info/v1.0.0/changepwd?name=test&pass=1&newpass=2
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

return [
    '_default_' => [
        /* 路由的规则 */
        '^\/?$' => '/home/index'
    ]
];

/* End of file rewrite.php */