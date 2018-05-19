<?php
/**
 * Class Number
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */

namespace Zink\Widget;

class Number extends Variable
{

    /**
     * 随机生成字符串（最长32位）
     * @param int $length ：字符串位数
     * @return int
     */
    public static function random($length)
    {
        $length = intval($length);
        // 随机N位字符
        $min = pow(10, ($length - 1));
        $max = $min * 10 - 1;
        return rand($min, $max);
    }

    public static function uniqid($length = 8)
    {
        $id = abs(crc32(uniqid()));
        $id = (string)$id;
        $len = strlen($id);
        if ($len > $length) {
            $id = substr($id, $len - $length);
        } else {
            while ($len < $length) {
                $id .= rand(0, 9);
                $len++;
            }
        }

        return $id;
    }


    /**
     * 智牛式向下取整 eq: 239.78 => 230
     * @param $number
     * @return float
     */
    public static function gszxFloor($number)
    {
        $integerNum = floor($number);
        return floor($integerNum / 10) * 10;
    }

    public static function fen2yuan($money)
    {
        return number_format($money / 100, 2, '.', '');
    }

    /**
     * 25.00=>25
     * 25.52=>25.52
     * 50.10=>50.1
     * 05.50=>5.5
     * @param $number
     * @param int $precision
     * @param string $decPoint
     * @param string $thousandsSep
     * @return string
     */
    function numberFormatClean($number, $precision = 0, $decPoint = '.', $thousandsSep = ',')
    {
        RETURN trim(number_format($number, $precision, $decPoint, $thousandsSep), '0' . $decPoint);
    }

}

/* End of file Number.php */