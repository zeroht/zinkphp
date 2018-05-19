<?php
/**
 * Clientapi 配置文件
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

return [
    /* api */
    'API_VERSION_BASE' => '1.0.0',            // api基础版本

    /* 通用配置 */
    'TPL_FILE_SUFFIX' => '.tpl',              // 默认模板后缀
    'TPL_FILE_DEFAULT' => 'status',           // 默认模板名

    /* session设置 */
    'SESSION_EXPIRE' => 86400*30              // 秒
];

/* End of file constant.php */