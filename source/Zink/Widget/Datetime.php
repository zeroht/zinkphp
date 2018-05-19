<?php
/**
 * Class Datetime
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */

namespace Zink\Widget;

class Datetime extends Variable
{
    const SECOND_OF_WEEK = 604800; //7 * 24 * 60 * 60
    /**
     * 利用周去重日历,不考虑 年月
     * @param array $datetimes
     * @param callable|null $format
     * @return array
     */
    public static function uniqueByWeek(array $datetimes, callable $format = NULL)
    {
        $list = [];
        foreach ($datetimes as $dt) {
            $timestamp = strtotime($dt);
            $key = date('NHi', $timestamp);
            $list[$key] = $format ? $format($timestamp) : $dt;
        }

//        sort($list, SORT_NUMERIC);
        return array_values($list);
    }

    public static function modifyEndDateExtendTime($date)
    {
        return $date ? $date . ' 23:59:59' : '';
    }

    /**
     * 当月月初时间
     * @return bool|string
     */
    public static function getBeginOfCurrentMonth()
    {
        return self::timeToSqlDateFormat(mktime(0, 0, 0, date('m'), 1, date('Y')));
    }

    /**
     * 当月月末时间(上月月末时间不能用类似方法)
     * @return bool|string
     */
    public static function getEndOfCurrentMonth()
    {
        return self::timeToSqlDateFormat(mktime(23, 59, 59, date('m'), date('t'), date('Y')));
    }

    /**
     * 上月月初时间
     * @return bool|string
     */
    public static function getBeginOfLastMonth()
    {
        return self::timeToSqlDateFormat(mktime(0, 0, 0, date('m') - 1, 1, date('Y')));
    }

    /**
     * 上月月末时间
     * @return bool|string
     */
    public static function getEndOfLastMonth()
    {
        return self::timeToSqlDateFormat(mktime(0, 0, 0, date('m'), 1, date('Y')) - 1);
    }


    public static function timeToSqlDateFormat($time)
    {
        return date('Y-m-d H:i:s', $time);
    }


    /**
     * 获取当前周的周一的日期
     * @return bool|string
     */
    public static function getBeginningOfCurrentWeek()
    {
        $weekday = date('w', time());
        $weekday = $weekday ? --$weekday : 6;
        return date("Y-m-d", strtotime("- {$weekday} day"));
    }

    /**
     * 获取当前周的周日的日期
     * @return bool|string
     */
    public static function getEndOfCurrentWeek()
    {
        $weekday = date('w', time());
        $weekday =  7 - ($weekday == 0 ? 7 : $weekday);
        return date("Y-m-d", strtotime("+ {$weekday} day"));
    }

    /**
     * 获取上周的周一的日期
     * @return bool|string
     */
    public static function getBeginningOfLastWeek()
    {
        return date('Y-m-d', strtotime('-1 monday', time()));
    }

    /**
     * 获取当前周的周日的日期
     * @return bool|string
     */
    public static function getEndOfLastWeek()
    {
        return date('Y-m-d', strtotime('-1 sunday', time()));
    }

    /**
     * 检验时间戳对应的日期是否是月初
     * @param $time
     * @return bool
     */
    public static function isBeginningOfMonth($time = NULL)
    {
        $time = $time ? $time : time();
        $day = date('d', $time);
        return $day == '01';
    }

    /**
     * 检验时间戳对应的日期是否是周一
     * @param $time
     * @return bool
     */
    public static function isMonday($time = NULL)
    {
        return self::_isEqualWeekday(1, $time);
    }

    public static function isTuesday($time = NULL)
    {
        return self::_isEqualWeekday(2, $time);
    }


    private static function _isEqualWeekday($equalWeekday, $time = NULL)
    {
        $time = $time ? $time : time();
        $weekday = date('w', $time);
        return $weekday == $equalWeekday;
    }


    /**
     * 获取两个日期的日期差
     * @param $startDate (时间戳)
     * @param $endDate (时间戳)
     * @return float
     */
    public static function getDiffDaysBetween2date($startDate, $endDate)
    {
        $startDate = date('Y-m-d', $startDate);
        $endDate = date('Y-m-d', $endDate);
        $startDateArr = explode("-", $startDate);
        $endDateArr = explode("-", $endDate);
        $start = mktime(0, 0, 0, $startDateArr[1], $startDateArr[2], $startDateArr[0]);
        $end = mktime(0, 0, 0, $endDateArr[1], $endDateArr[2], $endDateArr[0]);
        $days = round(($end - $start) / 3600 / 24);
        return $days;
    }


    /**
     * 获取某个日期对应月的月末
     * @param $date (时间戳)
     * @return bool|string
     */
    public static function getLastDayOfMonth($date)
    {
        $firstDay = date('Y-m-01', $date);
        $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
        return $lastDay;
    }


    /**
     * 获取某个日期对应周的最后一天(周日)
     * @param $date (时间戳)
     * @return bool|string
     */
    public static function getLastDayOfWeek($date)
    {
        $date = date('Y-m-d', $date);
        $lastDay = date("Y-m-d", strtotime("$date Sunday"));
        return $lastDay;
    }

    public static function getDiffWeeksBetween2date($startTime, $endTime)
    {
        $res = floatval(strtotime($endTime) - strtotime($startTime));
        return ceil($res / self::SECOND_OF_WEEK);
    }

    public static function yesterday()
    {
        return date("Y-m-d", strtotime("-1 day"));
    }

    public static function tomorrow()
    {
        return date("Y-m-d", strtotime("+1 day"));
    }

    public static function today()
    {
        return date("Y-m-d");
    }

    public static function now()
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * @param $time: 时间戳
     * @return mixed
     */
    public static function getWeekShowText($time)
    {
        $week = date('w', $time);
        $weekMap = [
            0 => '周日',
            1 => '周一',
            2 => '周二',
            3 => '周三',
            4 => '周四',
            5 => '周五',
            6 => '周六',
        ];

        return $weekMap[$week];
    }

    public static function getShowMonthDay($time)
    {
        $month = date('m', $time);
        if (substr($month, 0, 1) == 0) {
            $month = substr($month, 1, 1);
        }
        $day = date('d', $time);
        if (substr($day, 0, 1) == 0) {
            $day = substr($day, 1, 1);
        }

        return "{$month}月{$day}日";
    }


    public static function getNextMonthByTime($time)
    {
        if (is_string($time)){
            $time = strtotime($time);
        }

        return mktime(0 ,0 ,0 ,date('m', $time) + 1, date('d', $time), date('Y', $time));
    }

    public static function getBeginningOfToday()
    {
        return date('Y-m-d') . ' 00:00:00';
    }

    public static function getEndOfToday()
    {
        return date('Y-m-d') . ' 23:59:59';
    }
}

/* End of file Datetime.php */