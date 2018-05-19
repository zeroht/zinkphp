<?php
/**
 * Javascript视图类
 * 输出javascript脚本
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

class JsView extends AbstractView
{
    public function display()
    {
        header('Content-Type: application/x-javascript;charset=utf-8');
        echo $this->fetch();
    }

    public function fetch()
    {
        return $this->_data;
    }
}

/* End of file TextView.php */
