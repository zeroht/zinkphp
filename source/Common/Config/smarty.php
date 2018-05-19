<?php
/**
 * smarty 常用配置
 *
 * @author:  thu
 * @date:    2016/5/6
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

return [
	'template_dir' => APP_PATH.'tpl/',
	'compile_dir' => APP_PATH.'tpl_c/',
	'left_delimiter' => '<{',
	'right_delimiter' => '}>',
	'debugging' => false,
	'default_val' => array(
        // 定义一些常量值
        'Z_APP_NAME' => APP_NAME,
        'Z_JS_VERSION' => Z_JS_VERSION
    )
];

/* End of file smarty.php */