<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */

namespace App\Admin\Controller;

use Common\ErrorCode;
use Zink\Core\Controller;

/**
 * Class UpdateAction
 * @package Zink\Core
 */
trait RemoveAction
{
    /**
     * 具体的数据删除方法,子类有save功能需要重写此方法
     * @return bool
     */
    protected function _remove(){
        return FALSE;
    }

    /**
     * 数据删除Action方法
     * @return \Zink\View\AbstractView
     */
    public function removeAction()
    {
        $errcode = $this->_remove();
        if (TRUE === $errcode){
            return Controller::json(ErrorCode::SUCCESS);
        } else if ($errcode === FALSE){
            return Controller::json(ErrorCode::ERR_500);
        } else if (is_subclass_of($errcode, AbstractView::class)){
            return $errcode;
        } else {
            return Controller::json($errcode);
        }
    }
}

/* End of file RemoveAction.php */
