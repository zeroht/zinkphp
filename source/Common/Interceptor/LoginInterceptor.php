<?php
/**
 *  判断用户是否已登录
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *  1. 2016/5/18 @thu: 创建；
 */

namespace Common\Interceptor;

use Common\Service\SessionService;
use Zink\Core\Action;
use Zink\Core\Interceptor;
use Zink\Widget\ApacheEnv;


class LoginInterceptor implements Interceptor
{

    public function intercept(Action &$action)
    {
        $controller = $action->getController();
        $uid = SessionService::getUid();
        if (!$uid) {
            return $controller->login();
        }

        ApacheEnv::setUid($uid);
        
        return $action->invoke();
    }

}

/* End of file LoginInterceptor.php */