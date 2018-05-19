<?php
/**
 * Class Str
 *  String是保留字段
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */


namespace Zink\Widget;

class Str extends Variable
{
    public function append($string, $link = '')
    {
        if ($link && $this->_val && $string){
            $this->_val .= $link;
        }

        $this->_val .= $string;

        return $this;
    }

    public function toEscape()
    {
        return addslashes($this->_val);
    }

    public function toCrc32($dechex = true)
    {
        $str = crc32($this->_val);
        return $dechex ? dechex($str) : $str;
    }

    public function toMd5()
    {
        return md5($this->_val);
    }

    /**
     * 字符串切分为数组
     * @param type $delimiter
     * @param type $unique:去重
     * @return type
     */
    public function split($delimiter, $unique = false)
    {
        if (empty($this->_val)){
            return array();
        }

        $arr = explode($delimiter, $this->_val);
        return $unique ? array_unique($arr) : $arr;
    }

    /**
    * 随机生成字符串（最长32位）
    * @param type $length：字符串位数
    * @return type
    */
   public static function random($length)
   {
        $length = intval($length);
        $string = md5(uniqid());
        return substr($string, rand(0, strlen($string) - $length), $length);
   }

   /*
    * 生成32位唯一字符传
    */
   public static function uniqid()
   {
       return md5(uniqid().  rand(0, 1000000));
   }

    /**
     * 去掉字符串结尾多余的字符
     *
     * @param $haystack
     * @param $needle
     * @return string
     */
    public static function removeLastChar($haystack, $needle)
    {
        if (strripos($haystack,$needle) && (strripos($haystack,$needle) == strlen($haystack) - 1)) {
            $haystack = substr($haystack,0,strlen($haystack) - 1);
        }
        return $haystack;
    }

    /**
     * 按数字顺序重拍字符串
     *
     * @param $haystack
     * @param $needle
     * @return string
     */
    public static function sortStrByNum($haystack, $needle)
    {
        $haystack = self::removeLastChar($haystack, $needle);
        $strArray = explode($needle,$haystack);
        sort($strArray,SORT_NUMERIC);
        $haystack = implode($needle,$strArray);
        return $haystack;
    }


    public static function getCity($value)
    {
        if (empty($value)){
            return $value;
        }
        $city = str_replace('市', '', $value);
        return $city;
    }

    public static function getProvince($value)
    {
        if (empty($value)){
            return $value;
        }
        $province = str_replace('省', '', $value);
        $province = str_replace('市', '', $province);
        return $province;
    }


    public static function isMobile($mobile)
    {
        $pattern = '/^1[3-9]\d{9}$/';
        $ret = preg_match($pattern, $mobile);
        return $ret ? TRUE : FALSE;
    }

    public static function fen2yuan($money)
    {
        $money = number_format($money/100, 2, '.', '');
//        return floatval($money);
        return $money;
    }

    public static function seconds2minutes($seconds)
    {
        return round($seconds/60);
    }

    public static function seconds2hours($seconds)
    {
        return round($seconds/3600);
    }

    public static function minutes2hours($minutes)
    {
        return round($minutes/60);
//        return $minutes/60;
    }

    public static function minutes2hoursNoRound($minutes)
    {
        return $minutes / 60;
    }

    /**
     * 格式时间
     * 2 * 24 * 60 * 60 + 2 * 60 * 60 + 3 * 60   =>  2天2小时3分钟
     * 2 * 60 * 60 + 3 * 60   =>  2小时3分钟
     * 3 * 60   =>  3分钟
     * @param $seconds
     * @return string
     */
    public static function seconds2Str($seconds)
    {
        $f = array(
            '86400' => '天',
            '3600'  => '小时',
            '60'    => '分钟'
        );
        $result = '';
        foreach ($f as $k => $v) {
            $c = floor($seconds / $k);

            if ($c != 0) {
                $result .= $c . $v;
            }
            $seconds = intval($seconds % $k);
        }
        return $result;
    }

    public static function getUserName($name, $mobile)
    {
        if (!$mobile){
            return '';
        }
        return $name ? $name : substr($mobile, -4);
    }

}

/* End of file Str.php */