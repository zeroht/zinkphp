<?php
/**
 * Smarty视图类
 * 输出smarty模本文本
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */


namespace Zink\View;

use Zink\Core\Debugger;
use Zink\Core\Smarty;
use Zink\Exception\NotFoundException;

class SmartyView extends AbstractView
{
    protected $_smarty = null;
    public function __construct($data, $tpl)
    { 
        $this->_smarty = Smarty::getInstance();
        if (!$this->_smarty->tplExist($tpl)) {
            throw new NotFoundException('Smarty Tpl Not Existed: ' . $tpl);
        }
        
        $this->_tpl = $tpl;
        $this->assign($data);
        //parent::__construct($data, $tpl);
    }
    
    public function display()
    {
        // 页面
        // linux 区分大小写，解析时名字转换成了小写，所以模板名字需小写
        header('Content-Type:text/html;charset=utf-8');
        $html = $this->fetch();

        echo $html;

        // 放到 html后面输出,防止影响页面布局
        if(!Debugger::isOnlineMode()){
            Debugger::console($this->_smarty->getTplVals());
        }
    }

    public function fetch()
    {
        // 页面
        // linux 区分大小写，解析时名字转换成了小写，所以模板名字需小写
        $this->_smarty->assign($this->_data);
        return $this->_smarty->fetch($this->_tpl);
    }
}

/* End of file SmartyView.php */
