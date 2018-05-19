<?php
/**
 * Socket类
 * 封装PHP的socket方法，可以通过端口进行通信
 *
 * @author  slwu
 * @since   2016-03
 */


namespace Zink\Widget;

use Zink\Core\Exception;
use Zink\Core\Log;

class Socket {
    public $link;//连接对象

    /**
     * Socket constructor
     * @param $port
     */
    public function __construct($host, $port) {
        if(empty($host) || empty($port)) {
            return False;
        }

        //创建socket实例
        $this->link = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        //尝试连接
        socket_connect($this->link, $host, $port);

        //初始化
        socket_set_option($this->link, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>180, "usec"=>0));
        socket_set_option($this->link, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>180, "usec"=>0));
    }

    /**
     * Socket send
     * @param $content
     * @return bool|int
     */
    public function send($content) {
        if(empty($this->link)) {
            return false;
        }

        $result = false;
        try {
            $result = socket_write($this->link, $content, strlen($content));
        } catch(Exception $error) {
            Log::getLogger("socket")->error($error);
        }

        return $result;
    }

    /**
     * Socket recv
     * @return bool|string
     */
    public function recv($len) {
        if(empty($this->link)) {
            return false;
        }
        
        $result = false;
        try {
            $result = socket_read($this->link, $len);
        } catch(Exception $error) {
            Log::getLogger("socket")->error($error);
        }

        return $result;
    }

    /**
     * Socket close
     * @return bool
     */
    public function close() {
        return socket_close($this->link);
    }
}
?>
