<?php
/**
 * Created by PhpStorm.
 * User: shaowangzhen
 * Date: 2017/6/3
 * Time: 12:08
 */

namespace App\Library;

class StringCommon
{
    /**
     * @desc 判断并转换字符编码，需 mb_string 模块支持。
     *
     * @param mixed $str 数据
     * @param string $encoding 要转换的编码类型
     * @return mixed 转换过的数据
     */
    public static function encodingConvert($str, $encoding = 'UTF-8') {
        if (is_array($str)) {
            $arr = [];
            foreach ($str as $key => $val) {
                $arr[$key] = self::encodingConvert($val, $encoding);
            }
            return $arr;
        }
        $_encoding = mb_detect_encoding($str, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5']);
        if ($_encoding == $encoding) {
            return $str;
        }
        try {
            $str = @mb_convert_encoding($str, $encoding, $_encoding);
        } catch (\Exception $e) {
            //nothing todo
        }
        return $str;
    }

    /**
     * 用 mb_strimwidth 来截取字符，使中英尽量对齐。
     *
     * @param string $str
     * @param int $start
     * @param int $width
     * @param string $trimMarker
     * @return string
     */
    public static function wsubstr($str, $start, $width, $trimMarker = '...') {
        $encoding = mb_detect_encoding($str, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5']);
        $encoding = $encoding ? $encoding : 'UTF-8';
        return mb_strimwidth($str, $start, $width, $trimMarker, $encoding);
    }

    /**
     * @desc 格式化只留数字
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function numeric($str) {
        return preg_replace("/\D/", '', $str);
    }

    /**
     * @desc 格式化去掉所有空格
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function trim($str) {
        return preg_replace("/[\s\n]+/", '', $str);
    }

}