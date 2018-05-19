<?php
/**
 * 脚本运行异常
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/12 @thu: 创建；
 */

namespace Zink\Exception;
use Zink\Core\Exception;

class ScriptException extends Exception
{
    public function __construct($message = 'Script Error', $code = 500)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $this->_logger->error($this->getMessage());
    }

}

/* End of file RequestException.php */