<?php

/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */
namespace App\Clientapi\Interceptor;

use Common\Service\SessionService;
use Zink\Core\Action;
use Zink\Core\Interceptor;
use Zink\Widget\ApacheEnv;


class LoginInterceptor implements Interceptor
{

    public function intercept(Action &$action)
    {
        $controller = $action->getController();
        $user = SessionService::getUserInfo();
        $uid = $user['id'];
        if (!$user || $uid != HEADER_UID) {
            return $controller->login();
        }

        ApacheEnv::setUid($uid);
        return $action->invoke();
    }

}

/* End of file LoginInterceptor.php */