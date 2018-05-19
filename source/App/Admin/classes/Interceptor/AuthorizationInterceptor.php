<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */

namespace App\Admin\Interceptor;

use Zink\Core\Action;
use Zink\Core\Interceptor;
use Zink\Core\Router;

class AuthorizationInterceptor implements Interceptor
{

    public function intercept(Action &$action)
    {
        $permission = Router::getAction();
        $hasPermission = true; // 权限检测
        if (!$hasPermission) {
            return $action->getController()->deny();
        }
        
        return $action->invoke();
    }

}

/* End of file AuthorizationInterceptor.php */
