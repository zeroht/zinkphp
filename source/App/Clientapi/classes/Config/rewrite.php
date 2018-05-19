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

return array(
    /* 
     * 去掉版本部分，转换为标准url，兼容默认的rewrite，版本当作参数传递
     * 【约定】添加参数‘v'传递版本信息，具体业务接口设计中要不可使用 'v'参数
     */
    '_default_' => [
        /* 路由的规则 */
        '\A\/([\/\w]+)(?:\/v(\d+\.\d+\.\d+))?\/(\w+)\??(.*)\z' => '/${1}/${3}?v=${2}'
    ]
);

/* End of file rewrite.php */