<?php

/**
 * Smarty Json视图类
 * 输出smarty json模本文本
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

class SmartyJsView extends SmartyView
{
    public function display()
    {
        header('Content-Type: application/x-javascript;charset=utf-8');
        echo $this->fetch();
    }

    public function fetch()
    {
        $this->_smarty->assign($this->_data);
        return $this->_smarty->fetch($this->_tpl);
    }
}

/* End of file SmartyJsView.php */
