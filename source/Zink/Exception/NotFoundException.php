<?php
/**
 * 资源未找到异常
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/12 @thu: 创建；
 */

namespace Zink\Exception;

use Zink\Core\Exception;

class NotFoundException extends Exception
{
    public function __construct($message = 'Not Found', $code = 404)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $this->_logger->warning('[NotFoundException]'.$this->getMessage());
    }

}

/* End of file NotFoundException.php */