<?php


namespace App\Library;
use Illuminate\Support\Facades\Mail;

class Common
{

    public static function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

    public static function sendMail($mailTitle, $mailContent, $mailTo, $mailFrom = '', $pathToFile = '', $display = '')
    {
        try {
            Mail::raw($mailContent, function ($message) use ($mailTitle, $mailFrom, $mailTo, $pathToFile, $display) {
                if (empty($mailFrom)) {
                    $mailFrom = config('mail.username');
                }
                $title = 'noah_debug';
                $message->from($mailFrom, $title);
                foreach ($mailTo as $address) {
                    $message->to($address);
                }
                $message->subject($title . '：' . $mailTitle);
                if (!empty($pathToFile)) {
                    if (empty($display)) {
                        $message->attach($pathToFile);
                    } else {
                        $message->attach($pathToFile, ['as' => $display]);
                    }
                }
            });
        } catch (\Exception $e) {

        }
    }
}
