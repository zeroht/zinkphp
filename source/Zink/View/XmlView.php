<?php
/**
 * Xml视图类
 * 输出xml文本
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

use Zink\Widget\Xml;

class XmlView extends AbstractView
{
    public function display()
    {
        // 返回xml格式数据
        header('Content-Type:text/xml; charset=utf-8');
        echo $this->fetch();
    }

    public function fetch()
    {
        return Xml::array2xml($this->_data);
    }
}

/* End of file XmlView.php */
