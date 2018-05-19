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
use Zink\Widget\Json;

class SmartyJsonView extends SmartyView
{
    public function display()
    {
        header('Content-Type: application/json;charset=utf-8');
        echo $this->fetch();
    }

    public function fetch()
    {
        $data = Json::escapeArray($this->_data);
        $this->_smarty->assign($data);
        $jsonString = $this->_smarty->fetch($this->_tpl);
        // 去掉不必要的空白, 减少数据量
        return Json::trim($jsonString);
    }
}

/* End of file SmartyJsonView.php */
