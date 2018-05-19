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
use Zink\Db\Result\Collection;
use Zink\Widget\Pager;

/**
 * Class SearchAction
 * @package Zink\Core
 */
trait DetailAction
{
    /**
     * 数据查询方法,子类有search功能需要重写此方法
     * @return bool|Pager|Collection
     */
    protected function _detail($id)
    {
        return FALSE;
    }

    /**
     * 数据详情接口
     * @return \Zink\View\AbstractView
     */
    public function detailAction()
    {
        $id = $this->get('id');
        if (empty($id)) {
            return  Controller::json(ErrorCode::ERR_501);
        }

        $data = $this->_detail($id);
        if(empty($data)){
            return Controller::json(ErrorCode::SUCCESS, []);
        } else if (ErrorCode::isValidCode($data)){
            return Controller::json($data);
        }

        return $this->adminView($data, false);
    }
}


/* End of file DetailAction.php */
