<?php
/**
 * Created by PhpStorm.
 * User: shaowangzhen
 * Date: 2017/6/3
 * Time: 12:10
 */
namespace App\Library;

class ArrayCommon
{
    /*
 * @desc 将对象转换为数据
 * @param $obj obj
 * @return array
 */
    public static function obj2arr($obj) {
        if (is_object($obj)) {
            $obj = (array) $obj;
            $obj = self::obj2arr($obj);
        } elseif (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $obj[$key] = self::obj2arr($value);
            }
        }
        return $obj;
    }



    /**
     * @desc 二维数组 根据某个字段排序
     * @param $arr 要排序的数组
     * @param $row 排序的列
     * @param string $type 排序的规则 默认是升序
     * @return array
     */
    public static function arrSort($arr, $row, $type = 'asc') {
        $arr_temp = [];
        $empty_row = [];
        foreach ($arr as $v) {
            if (empty($v[$row])) {
                $empty_row[] = $v;
            } else {
                $arr_temp[$v[$row]] = $v;
            }
        }
        if ($type == 'desc') {
            krsort($arr_temp);
        } else if ($type == 'asc') {
            ksort($arr_temp);
        }
        return array_merge($arr_temp, $empty_row);
    }

}