<?php
/**
 * Clinetapi 应用程序类 执行应用过程管理
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/23 @thu: 创建；
 */

/**
 * Api 应用程序类 执行应用过程管理
 * 可以在模式扩展中重新定义 但是必须具有Run方法接口
 */
namespace App\Clientapi;

use App\Clientapi\Widget\ApiHeader;
use Common\Constant;
use Zink\Core\Action;
use Zink\Core\App;
use Zink\Core\Debugger;
use Zink\Core\Exception;
use Zink\Core\Log;
use Zink\Core\Session;
use Zink\Core\View;
use Zink\Exception\NotFoundException;
use Zink\Exception\RequestException;
use Zink\Widget\Json;
use Zink\Widget\Request;

class ClientapiApp extends App
{

    /**
     * Clientapi 初始化
     * @throws NotFoundException
     */
    static public function init()
    {
        // 定义当前Header的系统常量
        define('HEADER_PID', ApiHeader::getProductId());
        define('HEADER_VID', ApiHeader::getVersionId());
        define('HEADER_PLATFORM', ApiHeader::getPlatform());
        define('HEADER_CID', ApiHeader::getCid());
        define('HEADER_UID', ApiHeader::getUid());
        define('HEADER_SID', ApiHeader::getSessionId());
        define('HEADER_UA', ApiHeader::getUa());
        define('HEADER_NETWORK', ApiHeader::getNetwork());
        /* …… */

        // URL调度
        $uri = Request::getUri();
        $host = Request::getHost();
        if (!ClientapiRouter::dispatch($uri, $host)) {
            throw new NotFoundException("invalid request: $uri");
        }
        
        /*
        *  客户端通过header把sessionid发送过来 
        *  用户登录后sessionid才会有值，非登录下不需要session支持
        *  用cid作为唯一标识。
        */
        $sid = ApiHeader::getSessionId();
        if ($sid) {
            Session::startSession($sid);
        }
    }

    /**
     * 执行现有的action方法
     * @throws ErrorException
     * @throws RequestException
     */
    public static function exec()
    {
        $controller = ClientapiRouter::getController();
        $method = ClientapiRouter::getMethod();
        $action = ClientapiRouter::getAction();

        if (is_subclass_of($controller, '\App\Clientapi\Controller\BaseClientapiController')) {
            $invocation = new Action($controller, $method, $action);
            $status = $invocation->invoke();

            $buffer = ob_get_clean();

            $controller->display($status);

            Debugger::output($buffer);     // 输出调试信息
        } else {
            if (!Debugger::isOnlineMode() && Constant::get('DEBUG_REQUEST_ENABLED')) {  //非生产环境,当允许记录请求信息时,记录
                $respond = array(
                    'errcode' => 201,
                    'errmsg' => 'invalid api: ' . Request::getUri()
                );
            }
            
            throw new RequestException("invalid api:" . Request::getUri());
        }
    }

    /**
     * 自定义异常处理
     * @access public
     * @param Exception $e 异常对象
     */
    public static function appException(\Exception $e)
    {
        $errcode = ActionCode::ERR_50003;
        $errmsg = $e->getMessage();

        if ($e instanceof Exception) {
            $e->log();
        } else {
            Log::getLogger()->fatal('appException:'.$errmsg);
        }

        $data = array(
            'errcode' => $errcode,
            'errmsg' => Debugger::isOnlineMode() ? '网络异常，请稍后再试' : $errmsg
        );

        $tpl = Constant::get('TPL_FILE_DEFAULT').Constant::get('TPL_FILE_SUFFIX');
        $view = View::getSmartyJsonView($tpl, $data);
        $view->display();
        parent::finish();
    }

}

/* End of file ClientapiApp.php */
