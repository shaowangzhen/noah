<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Mail;

class BaseRepository
{

    public function getUserInfo()
    {
        return UserRepository::getLoginInfo();
    }

    public function getUserId()
    {
        $users = UserRepository::getLoginInfo();
        $userId = isset($users['users']['masterid']) ? $users['users']['masterid'] : 0;
        return $userId;
    }

    public function getUserPowerList()
    {
        $users = UserRepository::getLoginInfo();
        return $users['powers'];
    }

    /*
     * @desc 发送邮件
     * @param $mailTitle string 邮件标题
     * @param $mailContent string 邮件内容
     * @param $mailFrom string 邮件发送方地址
     * @param $mailTo array 邮件目的地址
     *
     */
    public static function sendMail($mailTitle, $mailContent, $mailTo, $mailFrom = '')
    {
        Mail::raw($mailContent, function ($message) use ($mailTitle, $mailFrom, $mailTo) {
            if (empty($mailFrom)) {
                $mailFrom = config('mail.username');
            }
            $title = env('SITE_ENV') == 'testing' ? '【测试机】Noah' : 'Noah';
            $message->from($mailFrom, $title);
            foreach ($mailTo as $address) {
                $message->to($address);
            }
            $message->subject($mailTitle);
        });
    }

    public static function sendMailHtml($view,$mailTitle, $mailContent, $mailTo, $mailFrom = ''){
        Mail::send(['html'=>$view], $mailContent, function ($message) use ($mailTitle, $mailFrom, $mailTo) {
            if (empty($mailFrom)) {
                $mailFrom = config('mail.username');
            }
            $title = env('SITE_ENV') == 'testing' ? '【测试机】Noah' : 'Noah';
            $message->from($mailFrom, $title);
            foreach ($mailTo as $address) {
                $message->to($address);
            }
            $message->subject($mailTitle);
        });
    }
}
