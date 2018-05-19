<?php
/**
 * Action控制类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\Core;
use Zink\Exception\NotFoundException;
use Zink\Exception\RuntimeException;
use Zink\Widget\Request;

class Action {
	private $_controller = null;
	private $_method = null;
    
    private $_interceptor = null;

    private $_iter = 0;
    
    /**
     * @param Controller $controller Controller实例
     * @param string $method Controller 执行的Controller方法
     * @param string $action action名，eq：/user/info/show
     */
    public function __construct($controller, $method, $action)
	{
		$this->_controller = $controller;
		$this->_method = $method;
        $this->_interceptor = Config::loadInterceptor($action);
	}
    
    /**
     * 
     * @return Controller
     */
    public function getController()
    {
        return $this->_controller;
    }

    public function getActionName()
    {
        return substr($this->_method, 0, strlen($this->_method)-6);
    }
    
    private function _invokeInterceptor($classname)
    {
        //$classFile
        // 命名约定
        $appName = APP_NAME;
        $appClassName = "\App\\{$appName}\Interceptor\\{$classname}";
        $commonClassname = "\Common\Interceptor\\{$classname}";
        if (Autoloader::import($appClassName)){
            $interceptor = new $appClassName();
        }else if (Autoloader::import($commonClassname)){
            $interceptor = new $commonClassname();
        }else {
            throw new RuntimeException("Interceptor '$classname' Not Exist.");
        }

        
        return $interceptor->intercept($this);
    }

    /**
     * @return mixed
     * @throws RuntimeException
     * @throws \Zink\Exception\NotFoundException
     */
	public function invoke()
	{
        // 处理拦截器
		if (is_array($this->_interceptor) && $this->_iter < count($this->_interceptor)){
			// 递归调用 拦截器
			$classname = $this->_interceptor[$this->_iter++];
			if ($classname){
                return $this->_invokeInterceptor($classname);
			}
		}
 
        $method = $this->_method;
        if  (!method_exists($this->_controller,$method)){
            throw new NotFoundException("invalid method($method):". Request::getUri());
        }

        return $this->_controller->$method();
	}
}

/* End of file Action.php */