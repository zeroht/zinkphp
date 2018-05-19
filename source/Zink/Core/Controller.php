<?php
/**
 * 控制器的抽象基类，所有控制器必须继承此类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/7 @thu: 创建；
 */


namespace Zink\Core;

use Common\Constant;
use Common\ErrorCode;
use Zink\Widget\IteratorSupport;
use Zink\Widget\Request;
use Zink\Widget\Validate;

abstract class Controller
{
    /* 保留返回值 */
    const AS_SUCCESS = 'success';           // 成功
    
    private $_params = array();               // GET、POST参数数组(只读)
    protected $_view_data = array();

    /**
     * 构造函数，传入默认的参数
     * @param type $defaultParams 
     */
    public function __construct($defaultParams = null)
    {
        $this->set($defaultParams);
    }

    public function getRules()
    {
        return [];
    }
    
    /**
     * 获取参数
     * @param null $name
     * @param null $filters
     * @param string $default
     * @return array|mixed|string
     */
    public function get($name = null, $filters = null, $default = '')
    {
        if (empty($name)) {
            return $this->_params;
        }

        $name = strtolower($name);
        if (isset($this->_params[$name]) && $this->_params[$name] !== '') {
            $data = $this->_params[$name];
            if ($filters) {  // 增加多方法过滤支持
                $filters = explode(',', $filters);
                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        $data = is_array($data) ? array_map($filter, $data) : $filter($data); // 参数过滤
                    }
                }
            }

            return $data;
        }

        return $default;
    }

    public function getInt($name, $default = 0)
    {
        return $this->get($name, 'intval', $default);    
    }

    public function getDate($name, $toDayEnd = false)
    {
        $day = $this->get($name);
        if (!Validate::isDate($day)){
            return "";
        }
        
        if ($toDayEnd){
            return date('Y-m-d 23:59:59', strtotime($day));
        } else {
            return date('Y-m-d 00:00:00', strtotime($day));
        }
    }

    public function getArray($name, $glue = ',', $unique = true)
    {
        $value = $this->get($name);
        if (empty($value)){
            return [];
        }
        
        if (!is_array($value)){
            $value = explode($glue, $value);
        }

        return $unique ? array_unique($value) : $value;
    }

    public function getCity($name)
    {
        $value = $this->get($name);
        if (empty($value)){
            return $value;
        }
        $city = str_replace('市', '', $value);
        return $city;
    }

    public function getLimitInt($name, $default = 0, $limit = 100)
    {
        $value = $this->get($name, 'intval', $default);
        if (empty($value)) {
            return $value;
        }
        return $value > $limit ? $limit : $value;
    }

    /**
     * 缓存Smarty变量数组赋值
     * @param array|string $key the template variable name(s)
     * @param mixed $value the value to assign
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if ($k != '') {
                    $this->_params[strtolower($k)] = $v;
                }
            }
        } else if (!empty($key)) {
            $this->_params[strtolower($key)] = $value;
        }
    }

    public function assign($tpl_var, $value = null)
    {
        if ($tpl_var instanceof IteratorSupport) {
            $tpl_var = $tpl_var->toArray();
        }

        if ($value instanceof IteratorSupport) {
            $value = $value->toArray();
        }

        if (is_array($tpl_var)) {
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_view_data[$key] = $val;
                }
            }
        } else if ($tpl_var) {
            $this->_view_data[$tpl_var] = $value;
        }
    }

    /**
     * 检测Action所需的参数是否齐备
     * @param: string $params: 参数，多个参数用‘,’分割; 不能有空格
     * @return bool
     */
    protected function _checkParams($params)
    {
        $par_array = explode(',', $params);
        foreach ($par_array as $p) {
            $p = strtolower($p);  // 全小写
            if (!isset($this->_params[$p]) || '' === $this->_params[$p]) {
                return FALSE;
            }
        }

        return TRUE;
    }
    
    public static function json($errcode = ErrorCode::SUCCESS, $data = null)
    {
        if ($data instanceof IteratorSupport){
            $data = $data->toArray();
        }

        $response = [
            'errcode' => $errcode,
            'errmsg' => ErrorCode::getMessage($errcode)
        ];

        if (isset($data)){
            $response['data'] = $data;
        }
        
        return View::getJsonView($response);
    }

    public static function custom($errcode = ErrorCode::SUCCESS, $data = [])
    {
        $response = [
            'errcode' => $errcode,
            'errmsg' => ErrorCode::getMessage($errcode)
        ];
        if ($data){
            foreach (array_keys($data) as $key) {
                $response[$key] = $data[$key];
            }
        }
        return View::getJsonView($response);
    }
    
    /**
     * 页面跳转
     * @param type $url
     * @return type
     */
    public function redirect($url)
    {
        return App::redirect($url);
    }

    /**
     * 需要授权调用此方法
     * @return type
     */
    public function deny()
    {
        if (Request::isAjaxJson()) {
            return self::json(ErrorCode::ERR_403);
        } else {
            return $this->redirect('/');
        }
    }

    public function customMessage($msg, $code)
    {
        if (Request::isAjaxJson()) {
            return $this->error(ErrorCode::customCode($code, $msg));
        } else {
            return $this->redirect('/');
            //return $this->view("index");
        }
    }

    /**
     * 请求成功
     * @param string|array $data
     * @return \Zink\View\AbstractView
     */
    public function success($data = null)
    {
        if (Request::isAjaxJson()) {
            return self::json(ErrorCode::SUCCESS, $data);
        }else {
            return $this->view();
        }
    }
    
    /**
     * 错误页面
     * @return type
     */
    public function error($errcode = ErrorCode::ERR_500)
    {
        if (Request::isAjaxJson()) {
            return self::json($errcode);
        } else {
            return $this->redirect(Constant::get('ERROR_PAGE'));
        }
    }

    /**
     * 需要登录调用此方法
     * @return type
     */
    public function login()
    {
        if (Request::isAjaxJson()) {
            return self::json(ErrorCode::ERR_402);
        } else {
            return $this->redirect('/');
        }
    }

    public function view($tpl = null)
    {
        $tpl = $tpl ? $tpl : Router::getTplFile(false);
        $tpl .= Constant::get('TPL_FILE_SUFFIX');
        return View::getSmartyView($tpl, $this->_view_data);
    }

    public function jsonview($tpl = null)
    {
        $tpl = $tpl ? $tpl : Router::getTplFile(false);
        $tpl .= Constant::get('TPL_JSON_SUFFIX');
        return View::getSmartyJsonView($tpl, $this->_view_data);
    }

    public function jsview($tpl = null)
    {
        $tpl = $tpl ? $tpl : Router::getTplFile(false);
        $tpl .= Constant::get('TPL_FILE_SUFFIX');
        return View::getSmartyJsView($tpl, $this->_view_data);
    }

    public function errorMessage($message)
    {
        return ErrorCode::err500($message);
    }
}

/* End of file Controller.php */
