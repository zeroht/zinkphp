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
use Zink\Widget\IteratorSupport;

/**
 * Class UpdateAction
 * @package Zink\Core
 */
trait UpdateAction
{
    /**
     * 数据修改方法,子类有update功能需要重写此方法
     * @return bool
     */
    protected function _update()
    {
        return FALSE;
    }

    /**
     * 更新单条记录
     * @param $name
     * @param $value
     * @return bool
     */
    protected function _updateOneKey($id, $name, $value){
        return FALSE;
    }
    
    /**
     * 数据修改方法
     * @return \Zink\View\AbstractView
     */
    public function updateAction()
    {
        $updateMode = $this->get('_only_update_one_key_');
        if ($updateMode == 1){
            /*
             * 只修改单个属性
             * body : {"__update__mode__" : "1", "id":"1", "name" : "name" , "value" : "zhangsan"}
             */
            $result = $this->_updateOneKey($this->get('id'), $this->get('name'), $this->get('value'));
        } else {
            $result = $this->_update();
        }
        
        if (TRUE === $result){
            return Controller::json(ErrorCode::SUCCESS);
        } else if ($result === FALSE){
            return Controller::json(ErrorCode::ERR_500);
        } else if (ErrorCode::isValidCode($result)){
            return Controller::json($result);
        }  else if (is_subclass_of($result, AbstractView::class)){
            return $result;
        } else {
            $data = ($result instanceof IteratorSupport) ? $result->toArray() : $result;
            return Controller::json(ErrorCode::SUCCESS, $data);
        }
    }
    
}

/* End of file UpdateAction.php */
