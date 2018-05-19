<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */

namespace App\Admin\Controller;

use Common\Service\SessionService;

class HomeController extends BaseAdminController
{
    public function indexAction()
    {
        if (SessionService::getUid()){
            return $this->view("home/admin");
        } else {
            return $this->view("home/login");
        }
    }
    /**
     * 登录后的主框架页面
     * @return \Zink\View\AbstractView
     */
    public function adminAction()
    {
        return $this->view();
    }

    /**
     * 首页登录页面
     * @return \Zink\Core\type|\Zink\View\AbstractView
     */
    public function loginAction()
    {
        return $this->view();
    }
}

/* End of file HomeController.php */
