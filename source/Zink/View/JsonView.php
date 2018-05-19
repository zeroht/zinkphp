<?php
/**
 * Json视图类
 * 输出json格式字符串
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

use Zink\Widget\Json;

class JsonView extends AbstractView
{
    public function display()
    {
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type: application/json;charset=utf-8');
        echo $this->fetch();
    }

    public function fetch()
    {
        $data = $this->_data;
        $jsonString = is_array($data) ? Json::array2json($data) : $data;
        return $jsonString;
    }
}

/* End of file JsonView.php */
