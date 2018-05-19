<?php
/**
 * 自定义异常类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/12 @thu: 创建；
 */

namespace Zink\Exception;

use Zink\Core\Exception;

class AuthException extends Exception
{
    public function __construct($message = 'Unauthorized', $code = 401)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $this->_logger->warning('[AuthException]'.$this->getMessage());
    }

}

/* End of file RequestException.php */