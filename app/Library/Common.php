<?php

/**
 * @desc 公共工具类
 *
 * Created by PhpStorm.
 * User: lixupeng
 * Date: 16/2/23
 * Time: 下午3:39
 */

namespace App\Library;

use GuzzleHttp\Client;

class Common {

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

    /**
     * @desc 查询结果集转变为键值对的存在
     * @param
     */
    public static function resultKey2value($result, $fieldKey, $fieldValue) {
        $arr = [];
        foreach ($result as $k => $v) {
            $arr[$v[$fieldKey]] = $v[$fieldValue];
        }
        return $arr;
    }

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

    /*
     * @desc 远程图片下载到本地
     *
     */

    public static function remotePicDownload($remotePic, $filename) {
        set_time_limit(0);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        $fp = fopen($remotePic, 'r');
        while (!feof($fp)) {
            echo fread($fp, 1024 * 100);
        }
        fclose($fp);
        return;
    }

    /**
     * 远程文件批量下载到本地
     *
     * @param array $fileUrls
     * @param string $downName
     */
    public static function remotePicDownloads($fileUrls, $downName) {
        set_time_limit(0);
        ini_set('memory_limit', '1000M');
        $filename = storage_path() . '/' . time() . rand(100, 999) . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($filename, \ZIPARCHIVE::CREATE) !== true) {
            exit('无法打开文件，或者文件创建失败');
        }
        $filenames = [];
        $mh = curl_multi_init();
        $ch = [];
        foreach ($fileUrls as $k => $val) {
            $ch[$k] = curl_init();
            curl_setopt($ch[$k], CURLOPT_URL, $val['url']);
            curl_setopt($ch[$k], CURLOPT_TIMEOUT, 30);
            curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, TRUE);
            curl_multi_add_handle($mh, $ch[$k]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        foreach ($fileUrls as $k => $val) {
            $temp = curl_multi_getcontent($ch[$k]);
            curl_multi_remove_handle($mh, $ch[$k]);
            //文件名重复处理
            if (array_key_exists($val['filename'], $filenames)) {
                $n = $filenames[$val['filename']];
                $fname = preg_replace('/(.\w+)$/', "($n)$1", $val['filename']);
                $filenames[$val['filename']] ++;
            } else {
                $filenames[$val['filename']] = 1;
                $fname = $val['filename'];
            }
            $temp && $zip->addFromString($fname, $temp);
        }
        curl_multi_close($mh);
        $zip->close();
        //下载
        if (!file_exists($filename)) {
            exit("无法找到文件，请联系管理员");
        }
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . $downName . '.zip');
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary"); //二进制文件
        header('Content-Length: ' . filesize($filename));
        @readfile($filename);
        unlink($filename);
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

    /**
     * @desc 获取精确的时间戳,以秒为单位
     * @return float
     */
    public static function microtimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * @desc 记录日志
     * @param string $filePath 文件路径,相对于项目日志目录
     * @param string $fileContent 文件内容
     * @return bool|int
     */
    public static function logFile($filePath, $fileContent) {
        $siteLogDir = config('common.siteLogDir') . date('Ym') . '/';
        $fileContent = date('Y-m-d H:i:s') . "\t" . $fileContent . "\r\n";
        $filePath = $siteLogDir . $filePath;
        $dirpath = dirname($filePath);
        self::makeDir($dirpath);
        return file_put_contents($filePath, $fileContent, FILE_APPEND);


        /*
          if(Storage::exists($filePath)){
          Storage::append($filePath, $fileContent);
          }else{
          Storage::put($filePath, $fileContent);
          }
         */
    }

    /**
     * 创建目录
     */
    public static function makeDir($dirpath) {
        if (is_dir($dirpath) === false) {
            mkdir($dirpath, 0777, true);
            chmod($dirpath, 0777);
        }
    }

    /*
     * 压缩文件
     * @param string $zippath 压缩后的zip文件的路径
     * @param array $filepath_arr 被压缩的文件路径,用数组传递
     * @return boolean
     */

    public static function archiveFile($zippath, $filepath_arr) {
        self::makeDir(dirname($zippath));
        $zip = new \ZipArchive();
        if ($zip->open($zippath, \ZipArchive::CREATE) === true) {
            foreach ($filepath_arr as $val) {
                if ($zip->addFile($val, basename($val)) === false) {
                    $zip->close();
                    return false;
                }
            }
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存文件
     * @param string $savepath 保存路径
     * @param string $content 文件内容
     * @return boolean
     */
    public static function saveFile($savepath, $content) {
        $dirpath = dirname($savepath);
        self::makeDir($dirpath);
        if (($fp = fopen($savepath, 'w')) === false) {
            return false;
        }
        if (fwrite($fp, $content) === false) {
            fclose($fp);
            return false;
        }
        fclose($fp);
        return true;
    }

    /*
     * 下载资料到本地服务器
     * @param string $fileurl 文件在远程服务器的url
     * @param string $savepath 下载到本地服务器的路径
     * @return boolean
     */

    public static function downloadFile($fileurl, $savepath) {
        //$client = new Client();
        for ($i = 0; $i < 3; $i++) {
            try {
                //$response = $client->request('GET',$fileurl,['timeout' => 30]);
                //$content = $response->getBody()->getContents();
                $content = self::curlGet($fileurl);
                return self::saveFile($savepath, $content);
            } catch (\Exception $e) {
                continue;
            }
        }
        return false;
    }

    /**
     * CURL GET请求
     * @param $url
     * @return bool|mixed
     */
    public static function curlGet($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        } else {
            curl_close($ch);
            if (!is_string($ret) || !strlen($ret)) {
                return false;
            }
            return $ret;
        }
    }

    /**
     * 签名生成函数
     * @param  [type] $data   参数数组
     * @param  string $secret 密钥
     * @param  string $undate 签名中是否使用日期
     * @return [type]         [description]
     */
    public static function createSn($data, $secret = '', $undate = false) {
        $debug = isset($data['debug']) ? $data['debug'] : '';
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
        if ($debug && $debug == $secret) {
            echo "md5 str:", $str;
            echo '<br>';
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
        if (isset($data['debug']) && $data['debug'] == $secret) {
            echo 'post data：<br>';
            print_r($data);
            echo '<br>';
            echo 'your SN : ', $sn, '<br/>';
            echo 'correct SN : ', $chsn, '<br/>';
            echo '<br>';
        }
        return $sn == $chsn;
    }

    /**
     * 向客户端推送消息
     *
     * @param type $user_id
     * @param type $city_id
     * @param type $carid
     * @param type $message
     * @param type $title
     * @return type
     */
    public static function sendToApp($user_id, $city_id, $carid, $message, $title = "优信二手车") {
        $clientConf = config('common.clientMsg');
        $secret = $clientConf['secret'];
        $api = $clientConf['api'];
        $params = array(
            'carid' => $carid,
            'cityid' => $city_id,
            'text' => urlencode($message),
            'title' => urlencode($title),
            'type' => $clientConf['apptype'],
            'userid' => $user_id,
        );
        $params['token'] = self::createSn($params, $secret);
        $client = new Client();
        $respone = $client->request('post', $api, ['form_params' => $params]);
        $send_ret = $respone->getBody()->getContents();
        if ($send_ret) {
            $send_ret = json_decode($send_ret, true);
            return intval($send_ret['code']);
        }
        return -1;
    }

    /**
     * 验证是否手机号
     * @param type $mobile
     * @return boolean
     */
    public static function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

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

    /*
     * 设置特定列做数组key
     */

    public static function setArrayKey($arr, $key) {
        $data = [];
        foreach ($arr as $temp) {
            $data[$temp[$key]] = $temp;
        }
        unset($arr);
        return $data;
    }

    /*
     * 获取文件是否是图片
     */

    public static function judgePicFile($filename) {
        $picType = ['BMP', 'JPG', 'JPEG', 'PNG', 'GIF'];
        $type = pathinfo($filename);
        $type = strtoupper($type['extension']);
        if (in_array($type, $picType)) {
            return true;
        }
        return false;
    }

    /**
     * 上传
     * @param $file $_FILES['file']
     * @param $_FILES[file] => Array
    (
    [name] => car3.jpeg
    [type] => image/jpeg
    [tmp_name] => /tmp/phpBTqcWY
    [error] => 0
    [size] => 79959
    )
     */
    public static function uploadFile($file) {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        try {
            if ($file['error'] != 0) {
                return ['code' => -1, 'message' => '文件上传错误，请重试'];
            }
            list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
            // 上传文件
            $client = new Client();
            $response = $client->request('POST', Config('upload.postUrl'), [
                'form_params' => [
                    'app' => Config('upload.app'),
                    'key' => Config('upload.key'),
                    'method' => 'buf',
                    'file' => file_get_contents($file['tmp_name']),
                    'ext' => pathinfo($file['name'], PATHINFO_EXTENSION),
                ]
            ]);
            $res = $response->getBody()->getContents();
            $res = json_decode($res, true);
            if ($res['code'] == 1) {
                return ['code' => 1, 'message' => '上传成功', 'file' => $res['file'], 'width' => $width, 'height' => $height, 'ext' => pathinfo($file['name'], PATHINFO_EXTENSION)];
            } else {
                return ['code' => -1, 'message' => '很遗憾，上传文件失败'];
            }
        } catch (\Exception $e) {
            return ['code' => -1, 'message' => $e->getMessage()];
        }
    }

    /**
     * 扩展名自定义的上传图片方法
     */
    public static function uploadFileDefined($file, $ext='jpg') {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        try {
            if ($file['error'] != 0) {
                return ['code' => -1, 'message' => '文件上传错误，请重试'];
            }
            list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
            // 上传文件
            $client = new Client();
            $response = $client->request('POST', Config('upload.postUrl'), [
                'form_params' => [
                    'app' => Config('upload.app'),
                    'key' => Config('upload.key'),
                    'method' => 'buf',
                    'file' => file_get_contents($file['tmp_name']),
                    'ext' => $ext,
                ]
            ]);
            $res = $response->getBody()->getContents();
            $res = json_decode($res, true);
            if ($res['code'] == 1) {
                return ['code' => 1, 'message' => '上传成功', 'file' => $res['file'], 'width' => $width, 'height' => $height, 'ext' => pathinfo($file['name'], PATHINFO_EXTENSION)];
            } else {
                return ['code' => -1, 'message' => '很遗憾，上传文件失败'];
            }
        } catch (\Exception $e) {
            return ['code' => -1, 'message' => $e->getMessage()];
        }
    }

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

    public static function getImgHost() {
        $hosts = config('common.imgHost');
        return $hosts[array_rand($hosts, 1)];
    }

    public static function getImgHostStatic() {
        $hosts = config('common.imgHostStatic');
        return $hosts[array_rand($hosts, 1)];
    }

    //获取七牛上传token
    public static function getQiniuUploadToken() {
        $qiniuConf = config('common.qiniuUploadToken');
        $time = time();
        $sign = md5($qiniuConf['bucket'] . $time . $qiniuConf['sign_key']);
        $url = $qiniuConf['host'] . '?m=' . $qiniuConf['m'] . '&bucket=' . $qiniuConf['bucket'] . '&time=' . $time . '&sign=' . $sign;
        try {
            $ret = self::curlGet($url);
            if (!$ret) {
                return '';
            }
            $ret = json_decode($ret, true);
            if ($ret['code'] == 1) {
                return $ret['token'];
            } else {
                return '';
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    //获取七牛视频信息
    public static function getQiniuVideoInfo($videoName) {
        $qiniuHost = config('common.qiniuHost');
        $qiniuAvinfoUrl = 'http://' . $qiniuHost . '/' . $videoName . '?avinfo';
        try {
            $ret = self::curlGet($qiniuAvinfoUrl);
            if (!$ret) {
                return ['code' => -1, 'message' => '返回结果为空'];
            }
            $ret = json_decode($ret, true);
            if (!$ret['streams'] || !$ret['format']) {
                return ['code' => -1, 'message' => '返回结果错误'];
            }
            return [
                'code' => 1,
                'message' => '成功',
                'data' => [
                    'width' => $ret['streams'][0]['width'], //宽
                    'height' => $ret['streams'][0]['height'], //高
                    'duration' => round($ret['format']['duration']), //时长（秒）
                    'size' => $ret['format']['size'], //大小 (Byte)
                ]
            ];
        } catch (\Exception $e) {
            return ['code' => -1, 'message' => $e->getMessage()];
        }
    }

}
