<?php
/**
 * 异常处理类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/7 @thu: 创建；
 */

namespace Zink\Core;
class Exception extends \Exception
{
    protected $_logger = null;
    
    public function __construct($message, $code)
    {
        $this->_logger = Log::getLogger();
        parent::__construct($message, $code);
    }
    
    public function log()
    {
        $class = get_called_class();
        $this->_logger->error("[$class]".$this->getMessage());
    }

}

/* End of file Exception.php */