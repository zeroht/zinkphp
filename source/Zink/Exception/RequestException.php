<?php
/**
 * 请求异常类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/12 @thu: 创建；
 */

namespace Zink\Exception;

use Zink\Core\Exception;

class RequestException extends Exception
{
    public function __construct($message = 'Bad Request', $code = 404)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $this->_logger->warning('[RequestException]'.$this->getMessage());
    }

}

/* End of file RequestException.php */