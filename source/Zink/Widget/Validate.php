<?php

/**
 * 正则表达式类.
 *
 */
namespace Zink\Widget;
class Validate
{

    private static $_regex_rules = array(
        'require' => '/.+/',
        'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        //'url' => '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!#\w]*)?$/',
        'url' => '/^http(s?):\/\/.*/i',
        'currency' => '/^\d+(\.\d+)?$/',
        'number' => '/^\d+$/',
        'zip' => '/^\d{6}$/',
        'integer' => '/^[-\+]?\d+$/',
        'double' => '/^[-\+]?\d+(\.\d+)?$/',
        'english' => '/^[A-Za-z]+$/',
        'phone' => '/^(\+86 )?1[3-9][0-9]{9}$/', // 手机号
        'password' => '/^[0-9a-zA-Z]{6,16}$/',
        'date' => '/^\d{4}\-\d{2}\-\d{2}$/'
    );

    /**
     * 使用正则验证数据
     * @access public
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     * @return boolean
     */
    public static function regexValidate($value, $rule)
    {
        // 检查是否有内置的正则表达式
        if (isset(self::$_regex_rules[strtolower($rule)])) {
            $rule = self::$_regex_rules[strtolower($rule)];
        }

        return preg_match($rule, $value, $matchs);
    }

    /**
     * 验证数据 支持 in between equal length regex expire ip_allow ip_deny
     * @access public
     * @param string $value 验证数据
     * @param mixed $rule 验证表达式
     * @param string $type 验证方式 默认为正则验证
     * @return boolean
     */
    public static function validate($value, $rule, $type = 'regex')
    {
        switch (strtolower(trim($type))) {
            case 'in': // 验证是否在某个指定范围之内 逗号分隔字符串或者数组
                $range = is_array($rule) ? $rule : explode(',', $rule);
                return in_array($value, $range);
            case 'between': // 验证是否在某个范围
                if (is_array($rule)) {
                    $min = $rule[0];
                    $max = $rule[1];
                } else {
                    list($min, $max) = explode(',', $rule);
                }
                return $value >= $min && $value <= $max;
            case 'equal': // 验证是否等于某个值
                return $value == $rule;
            case 'length': // 验证长度
                $length = mb_strlen($value, 'utf-8'); // 当前数据长度
                if (strpos($rule, ',')) { // 长度区间
                    list($min, $max) = explode(',', $rule);
                    return $length >= $min && $length <= $max;
                } else {// 指定长度
                    return $length == $rule;
                }
            case 'expire':
                list($start, $end) = explode(',', $rule);
                if (!is_numeric($start))
                    $start = strtotime($start);
                if (!is_numeric($end))
                    $end = strtotime($end);
                return $_SERVER['REQUEST_TIME'] >= $start && $_SERVER['REQUEST_TIME'] <=
                        $end;
            case 'ip_allow': // IP 操作许可验证
                return in_array(z_client_ip(), explode(',', $rule));
            case 'ip_deny': // IP 操作禁止验证
                return !in_array(z_client_ip(), explode(',', $rule));
            case 'regex':
            default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
                // 检查附加规则
                return self::regexValidate($value, $rule);
        }
    }

    public static function isPhoneNumber($phone)
    {
        return self::regexValidate($phone, 'phone');
    }

    public static function isEmail($email)
    {
        return self::regexValidate($email, 'email');
    }
    
    public static function isUrl($url)
    {
        return self::regexValidate($url, 'url');
    }

    public static function isGpsLocation($location, &$lng = 0, &$lat = 0)
    {
        if (preg_match('/^([1-9]\d+\.\d+),([1-9]\d+\.\d+)$/', $location, $match)){
            $lng = $match[1];
            $lat = $match[2];
            return true;
        }
        
        return false;
    }

    public static function isDate($day)
    {
        return self::regexValidate($day, 'date');
    }

    public static function checkFormat($string, $pattern)
    {
        if ('Ym' == $pattern) { // 201503
            $pattern = '/^[12]\d{3}(0[1-9]|1[0-2])$/';
        }else if ('H:i' == $pattern) { // 12:00
            $pattern = '/^[012]\d:[0-5]\d$/';
        }else if ('lnglat' == $pattern){
            $pattern = '/^[1-9]\d+\.\d+,[1-9]\d+\.\d+$/';
        }

        return preg_match($pattern, $string);
    }
}
    