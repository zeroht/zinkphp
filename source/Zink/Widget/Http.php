<?php

/**
 * 
 */

namespace Zink\Widget;

use Common\Constant;
use Zink\Core\Log;

class Http
{

    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_TEXT = 'text';

    private $_uri = '';
    private $_contentType = '';
    private $_params = array();
    private $_header = array();
    private $_timeout = 5;

    public function __construct($uri, $contentType = self::TYPE_TEXT)
    {
        $this->_uri = $uri;
        $this->_contentType = $contentType;
    }

    public function addHeader($key, $value)
    {
        $this->_header[$key] = $value;
    }

    public function setParameter($key, $value)
    {
        $this->_params[$key] = $value;
    }

    public function setParamsJson($json) {
        $this->_params = $json;
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
    }

    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
    }

    private function _parseResult($result)
    {
        if ($this->_contentType == self::TYPE_JSON) {
            return Json::json2array($result);
        } else if ($this->_contentType == self::TYPE_XML) {
            return Xml::xml2array($result);
        }

        return $result;
    }

    /**
     * 
     * @param string $type
     * @return array
     */
    public function get()
    {
        $query = http_build_query($this->_params);
        $url = $query ? "{$this->_uri}?{$query}" : $this->_uri;

        // 设置超时限制的context
        $opts = array(
            'http' => array(
                'timeout' => $this->_timeout
        ));

        // 请求头信息
        if (!empty($this->_header)) {
            $opts['http']['header'] = array();
            foreach ($this->_header as $n => $v) {
                $opts['http']['header'][] = "$n:$v";
            }
        }

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);

        // debug 日志
        Log::getLogger(Constant::get('LOG_NAME_API'))->debug("{$url}:$result");
        //\Zink\Core\ZLog::getInstance(LOG_NAME_API)->debug("{$url}:$result");
        return $this->_parseResult($result);
    }
 
    /**
     * 
     * @param string $type
     * @return array
     */
    public function post2()
    {
        $data = $this->_params;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_URL, $this->_uri);
        
        // 请求头信息
        if (!empty($this->_header)) {
            $http_header = array();
            foreach ($this->_header as $n => $v) {
                $http_header[] = "{$n}:{$v}";
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $result = curl_exec($ch);
        curl_close($ch);

        Log::getLogger()->debug("{$this->_uri}:$result");
        //\Zink\Core\ZLog::getInstance()->debug("{$this->_uri}:$result");
        return $this->_parseResult($result);
    }

    public function post()
    {
        $data = http_build_query($this->_params);

        $http_header = array(
            "Content-type:application/x-www-form-urlencoded",
            "Content-Length:" . strlen($data)
        );

        // 请求头信息
        if (!empty($this->_header)) {
            foreach ($this->_header as $n => $v) {
                $http_header[] = "{$n}:{$v}";
            }
        }

        // 设置超时限制的context
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => $http_header,
                'content' => $data,
                'timeout' => $this->_timeout
        ));

        $context = stream_context_create($opts);
        $result = file_get_contents($this->_uri, false, $context);
        // debug 日志
        $url = $this->_uri."?".$data;
        Log::getLogger(Constant::get('LOG_NAME_API'))->debug("{$url}:\n{$result}");
        //\Zink\Core\ZLog::getInstance(LOG_NAME_API)->debug("{$url}:\n{$result}");
        return $this->_parseResult($result);
    }
}
