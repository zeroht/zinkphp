<?php
/**
 * 非法变量
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/17 @thu: 创建；
 */

namespace Zink\Exception;
use Zink\Core\Exception;

class UnexpectetException extends Exception
{

    protected $code = 503;
    protected $message = 'Run Error';
    
    public function __construct($message = 'Run Error', $code = 503)
    {
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $message = '[UnexpecteException]' . $this->getFile() . '(' . $this->getLine() . '):'
                . $this->getMessage();
        $this->_logger->fatal($message);
    }

}

/* End of file UnexpecteException.class.php */