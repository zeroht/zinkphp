<?php
/**
 * File视图类
 *  输出文件内容
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

use Zink\Exception\NotFoundException;

class FileView extends AbstractView
{
    public function __construct($data, $tpl)
    {
        if (!is_file($tpl)){
            throw new NotFoundException("File '$tpl' Not Found.");
        }
        
        parent::__construct($data, $tpl);
    }

    public function display()
    {
        echo $this->fetch();
    }

    public function fetch()
    {
        return file_get_contents($this->_tpl);
    }
}

/* End of file FileView.php */