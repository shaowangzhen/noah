<?php
/**
 * Created by PhpStorm.
 * User: shaowangzhen
 * Date: 2017/6/3
 * Time: 12:11
 */
namespace App\Library;

class TimeCommon
{
    public static function timeDiffToText($time) {
        $now = time();
        $timeDiff = self::timeDiff($time, $now);

        if ($timeDiff['day'] > 7 * 20) {
            $text = date('Y-m-d', $time);
        } elseif ($timeDiff['day'] >= 7 && $timeDiff['day'] < 7 * 20) {
            $week = $timeDiff['day'] % 7;
            $text = $week . '周前';
        } elseif ($timeDiff['day'] >= 1 && $timeDiff['day'] < 7) {
            $text = $timeDiff['day'] . '天前';
        } else {
            if ($timeDiff['hour'] >= 1 && $timeDiff['hour'] < 24) {
                $text = $timeDiff['hour'] . '小时前';
            } else {
                if ($timeDiff['min'] >= 1 && $timeDiff['min'] < 60) {
                    $text = $timeDiff['min'] . '分钟前';
                } else {
                    $text = '刚刚';
                }
            }
        }

        return $text;
    }

    public static function timeDiff($begin_time, $end_time) {
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime = $end_time;
        } else {
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        $res = [ "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs];

        return $res;
    }
}