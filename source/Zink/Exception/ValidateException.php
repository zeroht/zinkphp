<?php
/**
 * 验证失败异常
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/12 @thu: 创建；
 */

namespace Zink\Exception;

use Zink\Core\Exception;

class ValidateException extends Exception
{
    public function __construct($message = 'Validate Failed', $code = 500)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        
    }
}

/* End of file RequestException.php */