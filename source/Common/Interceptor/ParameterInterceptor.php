<?php
/**
 *  通用参数拦截器
 * 对$_GET、$_POST参数进行过滤，同时将过滤后的参数传递给Action
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *  1. 2016/5/18 @thu: 创建；
 */

namespace Common\Interceptor;

use Zink\Core\Action;
use Zink\Core\Interceptor;
use Zink\Widget\Json;
use Zink\Widget\Request;

class ParameterInterceptor implements Interceptor
{

    public function intercept(Action &$action)
    {
        $controller = $action->getController();
        if (Request::isPost()){
            $paras = isset($_POST['body']) ? Json::json2array($_POST['body']) : [];
        }else {
            $paras = array_change_key_case($_GET);
        }

        $filterParas = [];
        foreach ($paras as $key => $data){
            $key = trim($key);
            if (Request::isRtpPost()) {
                $filterParas[$key] = Request::filterXss($data);
            } else {
                $filterParas[$key] = Request::filterHtml($data);
            }

        }

        $controller->set($filterParas);

        return $action->invoke();
    }
}

/* End of file ParameterInterceptor.php */