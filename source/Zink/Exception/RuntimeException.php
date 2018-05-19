<?php
/**
 * 运行时异常
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/7 @thu: 创建；
 */

namespace Zink\Exception;

use Zink\Core\Exception;

class RuntimeException extends Exception
{

    protected $code = 503;
    protected $message = 'Run Error';
    
    public function __construct($message = 'Run Error', $code = 503)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $message = '[RuntimeException]' . $this->getFile() . '(' . $this->getLine() . '):'
                . $this->getMessage();
        $this->_logger->fatal($message);
    }

}

/* End of file RuntimeException.php */