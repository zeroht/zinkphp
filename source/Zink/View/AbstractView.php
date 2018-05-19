<?php
/**
 *
 * 视图抽象基类
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

use Zink\Core\ArrayObject;

abstract class AbstractView
{
    /* 默认的几个视图文件类型 */
    const TYPE_FILE = 'file';
    const TYPE_TEXT = 'text';
    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_JS = 'js';
    const TYPE_SMARTY = 'smarty';
    const TYPE_SMARTY_JSON = 'smarty_json';
    const TYPE_SMARTY_XML = 'smarty_xml';
    const TYPE_SMARTY_JS = 'smarty_js';
    const TYPE_EXCEL = 'excel';
    
    protected $_tpl = null;
    protected $_data = '';

    /**
     * AbstractView constructor.
     * @param string|array $data 数据
     * @param string $tpl 模板文件
     */
    public function __construct($data, $tpl = null)
    {
        $this->_data = $data;
        $this->_tpl = $tpl;
    }

    /**
     * 缓存变量数组赋值
     * @param array|string $tpl_var the template variable name(s)
     * @param mixed $value the value to assign
     */
    public function assign($tpl_var, $value = null)
    {
        if ($tpl_var instanceof ArrayObject) {
            $tpl_var = $tpl_var->toArray();
        }

        if ($value instanceof ArrayObject) {
            $value = $value->toArray();
        }

        if (is_array($tpl_var)) {
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_data[$key] = $val;
                }
            }
        } else if ($tpl_var) {
            $this->_data[$tpl_var] = $value;
        }
    }

    /**
     * 输出视图
     * @return void
     */
    abstract public function display();

    /**
     * 读取视图数据
     * @return string 视图流数据
     */
    abstract public function fetch();
}

/* End of file AbstractView.php */
