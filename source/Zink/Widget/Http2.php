<?php

/**
 * 新封装的类,对Http做了优化,暂时不敢直接替换,带业务代码全部替换后
 * 干掉Http类,重命名
 */

namespace Zink\Widget;

use Zink\Core\Log;

class Http2
{

    const TYPE_JSON = 'json';
    const TYPE_XML = 'xml';
    const TYPE_TEXT = 'text';

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    private $_uri = '';
    private $_contentType = 'text';

    private $_header = array();
    private $_query = [];
    private $_data = [];
    private $_timeout = 5;

    public function __construct($uri, $contentType = self::TYPE_TEXT, $charset = 'utf-8')
    {
        $this->_uri = $uri;
        $this->_contentType = $contentType;

        $this->addHeader('charset', $charset);
    }

    public function addHeader($key, $value)
    {
        $this->_header[$key] = $value;
    }

    public function addQuery($key, $value)
    {
        $this->_query[$key] = $value;
    }

    public function addData($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * 内部api约定的特有格式,把整个 data转成json格式的字符串放入单个body参数中传递
     * @param $data
     */
    public function setBodyData($data) {
        $this->addData('body', Json::array2json($data));
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
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
     * @param $method
     * @return array|mixed|type
     */
    private function _request($method)
    {
        $url = $this->_uri;
        if (!empty($this->_query)){
            $query = [];
            foreach ($this->_query as $key => $value) {
                $query[] = $key.'='.urlencode($value);
            }

            $url .= '?'.implode('&', $query);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);

        // 请求头信息
        if (!empty($this->_header)) {
            $http_header = array();
            foreach ($this->_header as $n => $v) {
                $http_header[] = "{$n}:{$v}";
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = http_build_query($this->_data);
        switch ($method){
            case self::GET : {
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                break;
            }
            case self::POST: {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            }
            case self::PUT : {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            }
            case self::DELETE: {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            }
        }

        $result = curl_exec($ch);
        curl_close($ch);
        Log::getLogger()->debug("{$method} {$url}:$result");
        return $this->_parseResult($result);
    }

    public function get()
    {
        return $this->_request(self::GET);
    }

    public function post()
    {
        return $this->_request(self::POST);
    }

    public function put()
    {
        return $this->_request(self::PUT);
    }

    public function delete()
    {
        return $this->_request(self::DELETE);
    }
}

/* End of file Http.php */