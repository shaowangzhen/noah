<?php
/**
 * Created by PhpStorm.
 * User: shaowangzhen
 * Date: 2017/6/3
 * Time: 12:14
 */
namespace App\Library\Verification;

class ApiSn
{
    /**
     * 签名生成函数
     * @param  [type] $data   参数数组
     * @param  string $secret 密钥
     * @param  string $undate 签名中是否使用日期
     * @return [type]         [description]
     */
    public static function createSn($data, $secret = '', $undate = false) {
        unset($data ['sn'], $data['debug']);
        ksort($data);
        $str = '';
        foreach ($data as $key => $value) {
            $str .= "&$key=$value";
        }
        $str = trim($str, "&") . $secret;
        if (!$undate) {
            $str .= date('Y-m-d');
        }
        return strtolower(md5($str));
    }

    /**
     * 验签函数
     * @param  [type] $data   参数数组
     * @param  string $secret 密钥
     * @param  string $undate 签名中是否使用日期
     * @return [type]         [description]
     */
    public static function checkSn($data, $secret = '', $undate = false) {
        if (!is_array($data) || !isset($data ['sn'])) {
            return false;
        }
        $sn = strtolower($data ['sn']);
        $chsn = self::createSn($data, $secret, $undate);
        return $sn == $chsn;
    }
}