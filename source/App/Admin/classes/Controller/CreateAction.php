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
use Zink\View\AbstractView;
use Zink\Widget\IteratorSupport;

/**
 * Class CreateAction
 * @package Zink\Core
 */
trait CreateAction
{
    /**
     * 数据添加方法,子类有create功能需要重写此方法
     * @return bool|null|array|int
     */
    protected function _create(){
        return FALSE;
    }

    /**
     * 数据添加方法
     * @return \Zink\View\AbstractView
     */
    public function createAction()
    {
        $result = $this->_create();
        if (TRUE === $result){
            return Controller::json(ErrorCode::SUCCESS);
        } else if ($result === FALSE){
            return Controller::json(ErrorCode::ERR_500);
        } else if (ErrorCode::isValidCode($result)){
            return Controller::json($result);
        } else if (is_subclass_of($result, AbstractView::class)){
            return $result;
        } else {
            $data = ($result instanceof IteratorSupport) ? $result->toArray() : $result;
            return Controller::json(ErrorCode::SUCCESS, $data);
        }
    }
}

/* End of file CreateAction.php */
