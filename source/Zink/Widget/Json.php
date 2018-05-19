<?php

/**
 * Json 数据解析类 
 */
namespace Zink\Widget;
class Json
{
    /**
     * 数组转为json,先urlencode，再urldecode，解决中文乱码的问题
     * @param $arr
     * @param bool $object
     * @return string|type
     */
    public static function array2json($arr, $object = true)
    {
        if (empty($arr)) {
            return $object ? "{}" : "[]";
        }

        $arr = self::encodeArr($arr, true);
        $json = json_encode($arr);
        return self::encodeArr($json, false);
    }

    /**
     * json转为数组
     * @param $json
     * @return array|mixed
     */
    public static function json2array($json)
    {
        if (empty($json)) {
            return array();
        }

        return json_decode($json, true);
    }

    public static function escapeArray($data)
    {
        $replace = array(
            "\r" => "",
            "\n" => "\\n",
            "\t" => " ",
            "\\" => "\\\\",
            '"' => '\"'
        );

        if (is_string($data)) {
            $data = strtr($data, $replace);
            return $data;
        } else if (is_array($data)) {
            $newData = array();
            foreach ($data as $key => $val) {
                $newData[$key] = self::escapeArray($val);
            }

            return $newData;
        }

        return $data;
    }

    /**
     * urlencode/urldecode 字符
     * @param type $data
     * @param type $encode
     * @return type 
     */
    private static function encodeArr($data, $encode = true)
    {
        $replace = array(
            "\r" => "\\r",
            "\n" => "\\n",
            "\t" => " ",
            "\\" => "\\\\",
            '"' => '\"'
        );

        if (is_string($data)) {
            return $encode ? urlencode(strtr($data, $replace)) : urldecode($data);
        } else if (is_array($data)) {
            $newData = array();
            foreach ($data as $key => $val) {
                $key = $encode ? urlencode(strtr($key, $replace)) : urldecode($key);
                $newData[$key] = self::encodeArr($val, $encode);
            }

            return $newData;
        } else if ($data instanceof IteratorSupport){
            return self::encodeArr($data->toArray());
        } else {
            return $data;
        }
    }

    public static function trim($json)
    {
        $replace = array(
            "\r" => "",
            "\n" => "",
            "\t" => "",
            "    " => ""        // 编辑器用4个空格代替一个 tab
        );

        return strtr(trim($json), $replace);
    }
}
