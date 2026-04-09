<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Models\Menu;
use App\Models\MenuControl;




if (!function_exists('sendMail')) {
    function sendMail($send_to_name, $send_to_email, $subject, $body)
    {
        try {
            $mail_val = [
                'send_to_name' => $send_to_name,
                'send_to' => $send_to_email,
                'email_from' => env('MAIL_FROM_ADDRESS'),
                'email_from_name' => env('MAIL_FROM_NAME'),
                'subject' => $subject,
            ];

            Mail::send('email.mail', ['body' => $body], function ($send) use ($mail_val) {
                $send->from($mail_val['email_from'], $mail_val['email_from_name']);
                $send->replyto($mail_val['email_from'], $mail_val['email_from_name']);
                $send->to($mail_val['send_to'], $mail_val['send_to_name'])->subject($mail_val['subject']);
            });
            Log::info('Mail sent to ' . $send_to_email);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // echo "An error occurred while sending the email: " . $e->getMessage();
            return false;
        }
    }
}
if (!function_exists('sendMailAttachments')) {
    function sendMailAttachments($send_to_name, $send_to_email, $email_from_name, $subject, $body, $attachments = [])
    {
        try {
            $mail_val = [
                'send_to_name' => $send_to_name,
                'send_to' => $send_to_email,
                'email_from' => 'noreply@pancard.com',
                'email_from_name' => $email_from_name,
                'subject' => $subject,
            ];
            Mail::send('email.mail', ['body' => $body], function ($send) use ($mail_val, $attachments) {
                $send->from($mail_val['email_from'], $mail_val['email_from_name']);
                $send->replyto($mail_val['email_from'], $mail_val['email_from_name']);
                $send->to($mail_val['send_to'], $mail_val['send_to_name'])->subject($mail_val['subject']);
                foreach ($attachments as $attachment) {
                    if (!empty($attachment) && file_exists($attachment)) {
                        $send->attach($attachment);
                    }

                }
            });
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // echo "An error occurred while sending the email: " . $e->getMessage();
            return false;
        }
    }
}

if (!function_exists('getLeftMenu')) {
    function getLeftMenu()
    {
        if (Auth::user()->role == 1) {
            $menu = Menu::orderBy('seq_no', 'asc')->where('enable', '1')->get();
        } else {
            $menu = Menu::join('menu_controls', 'menus.id', '=', 'menu_controls.menu_id')
                ->where('menu_controls.user_id', Auth::user()->id)
                ->where('menus.enable', '1')
                ->select('menus.id', 'menus.seq_no', 'menus.name', 'menus.route', 'menus.image', 'menus.created_at', 'menus.updated_at')
                ->get();
        }
        return $menu;
    }
}
if (!function_exists('getAllMenu')) {
    function getAllMenu()
    {
        $menu = Menu::where('enable', '1')->get();
        return $menu;
    }
}

