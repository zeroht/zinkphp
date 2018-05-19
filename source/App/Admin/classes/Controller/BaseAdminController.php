<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */
namespace App\Admin\Controller;

use Common\Constant;
use Common\ErrorCode;
use Zink\Core\Controller;
use Zink\Core\Router;
use Zink\Core\Smarty;
use Zink\Core\View;
use Zink\Widget\Request;

/**
 * 后台基本类
 * Class BaseAdminController
 * @package App\Admin\Controller
 */
class BaseAdminController extends Controller
{
    /**
     * 各模块入口页面
     * @return \Zink\View\AbstractView
     */
    public function indexAction()
    {
        return parent::view();
    }
    
    /**
     * 需要登录
     * @return \Zink\Core\type|\Zink\View\AbstractView
     */
    public function login()
    {
        if (Request::isAjaxJson()) {
            return self::json(ErrorCode::ERR_402);
        } else {
            return $this->redirect("/");
        }
    }

    public function deny()
    {
        if (Request::isAjaxJson()) {
            return self::json(ErrorCode::ERR_403);
        } else {
            return $this->view("deny");
        }
    }

    public function customMessage($msg, $code)
    {
        if (Request::isAjaxJson()) {
            return $this->error(ErrorCode::customCode($code, $msg));
        } else {
            return $this->redirect('/');
        }
    }

    /**
     * 缺少必要参数
     * @return \Zink\Core\type
     */
    public function missingParameter()
    {
        return $this->error(ErrorCode::ERR_501);
    }


    public function errorMessage($msg)
    {
        return $this->error(ErrorCode::err500($msg));
    }
    

    public function needRefresh($msg)
    {
        return $this->error(ErrorCode::err610($msg));
    }

    /**
     * 后台查询类方法最后返回统一调用此视图方法,保持与前端代码逻辑一致
     * @param $data
     * @param bool $isSearch
     * @return \Zink\View\AbstractView
     */
    public function adminView($data, $isSearch = false)
    {
        if (Request::isAjaxJson()){
            // Ajax Json
            $extend = Constant::get('TPL_JSON_SUFFIX');
            $dataTpl = Router::getTplFile(false).$extend;
            $smarty = Smarty::getInstance();
            if ($smarty->tplExist($dataTpl)) {
                // 当模板文件存在时使用模板
                $data['tpl'] = $dataTpl;
                $tpl = $isSearch ? "search200".$extend : "detail200".$extend;
                return View::getSmartyJsonView($tpl , $data);
            } else {
                return Controller::json(ErrorCode::SUCCESS, $data);
            }
        } else {
            // html页面模板
            return $this->view();
        }
    }
}

/* End of file BaseAdminController.php */
