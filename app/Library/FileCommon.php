<?php
/**
 * Created by PhpStorm.
 * User: shaowangzhen
 * Date: 2017/6/3
 * Time: 12:06
 */
namespace App\Library;

class FileCommon
{
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
     * @desc 记录日志
     * @param string $filePath 文件路径,相对于项目日志目录
     * @param string $fileContent 文件内容
     * @return bool|int
     */
    public static function logFile($filePath, $fileContent) {
        $siteLogDir = '';
        $fileContent = date('Y-m-d H:i:s') . "\t" . $fileContent . "\r\n";
        $filePath = $siteLogDir . $filePath;
        $dirpath = dirname($filePath);
        self::makeDir($dirpath);
        return file_put_contents($filePath, $fileContent, FILE_APPEND);

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
}