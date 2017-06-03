<?php
/**
 * Created by PhpStorm.
 * User: shaowangzhen
 * Date: 2017/6/3
 * Time: 12:18
 */
namespace App\Library\Verification;

class CheckInput
{
    /*
 * 对post过来的数据做过滤
 * @param $data array
 * @return $new_data array
 */

    public static function filterStr($data) {
        if (!is_array($data)) {
            return addslashes(htmlspecialchars(strip_tags(trim($data))));
        } else {
            $new_data = [];
            foreach ($data as $key => $val) {
                $new_data[$key] = addslashes(htmlspecialchars(strip_tags(trim($val))));
            }
            unset($data);
            return $new_data;
        }
    }
}