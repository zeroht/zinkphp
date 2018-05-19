<?php
/**
 * aliyun的OCS实现
 * 参考：http://php.net/manual/zh/book.memcached.php
 * http://help.aliyun.com/knowledge_detail/5974153.html?spm=5176.788314989.3.12.fKK
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/6 @thu: 创建；
 */

namespace Zink\Cache;

use Common\Constant;

class OcsCache extends MemcachedCache{

    /**
     * Ocs connector, ocs 本身就是分布式的
     * @param string $server mem1.domain.com:11211
     * @return bool|\Memcached|null
     */
    protected function _connect($server) {
        if(!extension_loaded("Memcached")){
            throw new RuntimeException('Memcached Extension Not Existed.');
        }

        $ocs_id = Constant::get('MEMCACHE_OCS_ID');
        $ocs_password = Constant::get('MEMCACHE_OCS_PASSWORD');
        $memcached = new \Memcached($ocs_id);
        try {
            if (count($memcached->getServerList()) == 0) /*建立连接前，先判断*/ {
                /*所有option都要放在判断里面，因为有的option会导致重连，让长连接变短连接！*/
                $memcached->setOption(\Memcached::OPT_COMPRESSION, false);
                $memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);

                /* addServer 代码必须在判断里面，否则相当于重复建立’ocs’这个连接池，可能会导致客户端php程序异常*/
                list($host, $port) = explode(":", $server);
                if (!$memcached->addServer($host, $port)) {
                    $this->_log->error("Memcached $server connect failed.");
                    return FALSE;
                }

                if ($ocs_password){
                    $memcached->setSaslAuthData($ocs_id, $ocs_password);
                }
            }
        }catch (Exception $e) {
            $this->_log->error($e->getMessage());
            return FALSE;
        }
        
        return $memcached;
    }
}

/* End of file OcsCache.php */