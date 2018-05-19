<?php
/**
 * 所有Controller都必须继承这个类
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

namespace App\Clientapi\Controller;
use App\Clientapi\ClientapiRouter;
use Common\Constant;
use Zink\Core\Controller;
use App\Clientapi\ActionCode;
use Zink\Core\View;

class BaseClientapiController extends Controller
{
    /**
     *
     * @param $errcode
     */
    protected function _assignErrcode($errcode)
    {
        if ($errcode == self::AS_SUCCESS){
            $errcode = ActionCode::SUCCESS;
        }
        
        $errmsg = ActionCode::getMessage($errcode);
        $this->assign(array(
           'errcode' => $errcode,
           'errmsg' => $errmsg
        ));
    }

    /**
     * 数据数据
     * @param $status
     */
    public function display($status)
    {
        if (self::AS_SUCCESS == $status) {
            $tpl = ClientapiRouter::getTplFile();
            $this->_assignErrcode(ActionCode::SUCCESS);
        } else {
            $tpl = Constant::get('TPL_FILE_DEFAULT').Constant::get('TPL_FILE_SUFFIX');
            $this->_assignErrcode($status);
        }

        $view = View::getSmartyJsonView($tpl, $this->_view_data);
        $view->display();
    }

    /**
     * 需要用户登录
     * @return int
     */
    public function login()
    {
        return ActionCode::ERR_40002;
    }

    /**
     * 禁止访问
     * @return int
     */
    public function deny()
    {
        return ActionCode::ERR_40003;
    }

    public function missingParameter()
    {
        return ActionCode::ERR_50001;
    }

    public function errorMessage($message)
    {
        return ActionCode::err50000($message);
    }
}

/* End of file BaseClientapiController.php */