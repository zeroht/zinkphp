<?php
/**
 * Text视图类
 * 输出text文本
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

class TextView extends AbstractView
{
    public function display()
    {
        header('Content-Type: application/html;charset=utf-8');
        echo $this->fetch();
    }

    public function fetch()
    {
        return $this->_data;
    }
}

/* End of file TextView.php */
