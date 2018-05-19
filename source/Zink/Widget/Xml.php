<?php

/**
 * XML 数据解析类 
 */
namespace Zink\Widget;
class Xml
{

    /**
     * 将array 转成 xml 数据
     * @param array $arr
     * @param type $ignore_null
     * @return string 
     */
    public static function array2xml($arr, $ignore_null = true,
            $rootLevel = true)
    {
        $xml = $rootLevel ? '<?xml version="1.0" encoding="utf-8"?><root>' : '';
        if (is_string($arr)){
            $xml .= "$arr";
        }else if (is_array($arr)){
            foreach ($arr as $key => $value) {
                $value = $value;
                if (is_array($value)) {
                    $xml .="<$key>" . self::array2xml($value, $ignore_null, false) . "</$key>";
                } else if (!$ignore_null || !empty($value)) {
                    $xml .="<$key>" . self::escapeXML($value) . "</$key>";
                }
            }
        }
        $xml .= $rootLevel ? '</root>' : '';
        return $xml;
    }

    /**
     * XML 文本数据转换成 数组
     * @param type $xml
     * @return type 
     */
    public static function xml2array($xml, $bForceArr = false)
    {
        if (empty($xml)) {
            return array();
        }

        $valueArr = array(); //开始解析
        $indexArr = array();
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($parser, $xml, $valueArr, $indexArr);
        xml_parser_free($parser);
        $curValueIndex = 0; //用于表示当前要开始解析$valueArr数组的下表
        return self::parseXmlStruct($valueArr, $curValueIndex, count($valueArr),
                        $bForceArr);
    }

    /**
     * Xml数组结构递归处理函数
     * xml 最外层用<root></root>包裹
     * @param type $valueArr
     * @param type $curValueIndex
     * @param type $countValue
     * @return type 
     */
    private static function parseXmlStruct($valueArr, &$curValueIndex,
            $countValue, $bForceArr = false)
    {
        $childArr = array(); //存放孩子的数组
        while ($curValueIndex++ < $countValue) {
            $name = strtolower($valueArr[$curValueIndex]['tag']);  //获得标签的名字，弄成小写
            $size = isset($childArr[$name]) ? count($childArr[$name]) : 0; //如果有一样标签的兄弟，获得它在数组中的编号

            switch ($valueArr[$curValueIndex]['type']) {     //分3路进行处理
                case 'complete': {
                        // complete： 这个地方要注意了$bForceArr参数的作用
                        // 如果叶子节点重名，$bForceArr = false时， 后面的会覆盖前面的值，但如果
                        // 
                        $val = isset($valueArr[$curValueIndex]['value'])
                                    ? ($valueArr[$curValueIndex]['value']) : '';
                        if ($bForceArr) {
                            $childArr[$name][] = $val;
                        }else {
                            $childArr[$name] = $val;
                        }
                        
                        break;
                    }
                case 'open': $childArr[$name][$size] = self::parseXmlStruct($valueArr,
                                    $curValueIndex, $countValue);
                    break; //存在孩子，进行递归
                case 'close': return $childArr; //最后一个孩子
            }
        }

        return $childArr;
    }

    /**
     * 处理xml值里面的特殊字符
     * @param type $data
     * @return type 
     */
    public static function escapeXml($data)
    {
        if (is_string($data)) {
            //转义需要处理的字符
            $replace = array(
                "\r\n" => "",
                "\n" => "",
                "\t" => " "
                    //"<" => "&lt;",
                    //">" => "&gt;",
                    //"&" => "&amp;",
                    //"\"" => "&quot;",
                    //"'" => "&#39;"
            );
            $data = strtr($data, $replace);

            // 先解码，再转码，防止类似与 &gt;转成 &amp;gt
            $data = htmlspecialchars_decode($data);
            $data = htmlspecialchars($data);
            //删除控制字符,制表符、回车符、换行符除外
            //$data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $data);
            //清除非utf8字符
            //$data = iconv('UTF-8', 'UTF-8//IGNORE', $data);

            return $data;
        } else if (is_array($data)) {
            $newData = array();
            foreach ($data as $key => $val) {
                $newData[$key] = self::escapeXml($val);
            }

            return $newData;
        }

        return $data;
    }

    /**
     * 处理xml值里面CDATA的特殊字符
     * @param type $data
     * @return type 
     */
    public static function escapeXmlCData($data)
    {
        if (is_string($data)) {
            //转义需要处理的字符
            $replace = array(
                "<![CDATA[" => "",
                "]]>" => ""
            );

            return strtr($data, $replace);
        } else if (is_array($data)) {
            $newData = array();
            foreach ($data as $key => $val) {
                $newData[$key] = self::escapeXmlCData($val);
            }

            return $newData;
        }

        return $data;
    }

    /**
     * 转义xml文本里面的保留字段
     * 代码来自CI框架的 xml_convert函数
     * @param type $data
     * @return type 
     */
    public static function convertXmlString($data, $protect_all = FALSE)
    {
        if (is_string($data)) {
            $temp = '__TEMP_AMPERSANDS__';

            // Replace entities to temporary markers so that
            // ampersands won't get messed up
            $str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);

            if ($protect_all === TRUE) {
                $str = preg_replace("/&(\w+);/", "$temp\\1;", $str);
            }

            $str = str_replace(array("&", "<", ">", "\"", "'", "-"),
                    array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&#45;"),
                    $str);

            // Decode the temp markers back to entities
            $str = preg_replace("/$temp(\d+);/", "&#\\1;", $str);

            if ($protect_all === TRUE) {
                $str = preg_replace("/$temp(\w+);/", "&\\1;", $str);
            }

            return $str;
        } else if (is_array($data)) {
            $newData = array();
            foreach ($data as $key => $val) {
                $newData[$key] = self::convertXmlString($val, $protect_all);
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
        } else {
            return $data;
        }
    }

    public static function trim($xml)
    {
        return preg_replace('/>[\s]+</', '><', $xml);
    }
}
