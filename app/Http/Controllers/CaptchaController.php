<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    function verify_captcha(Request $request)
    {
        //init curl
        $ch = curl_init();
        //curl_setopt可以設定curl參數
        //設定url
        curl_setopt($ch , CURLOPT_URL , "https://www.google.com/recaptcha/api/siteverify");
        //設定header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
        //不直接出現curl結果的回傳值
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); 
        //啟用POST
        curl_setopt($ch, CURLOPT_POST, true);
        //傳入POST參數
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( array( "secret" => env('CAPTCHA_SECRET'), "response" => $request->id) ));
        //執行，並將結果存回
        $result = curl_exec($ch);
        //關閉連線
        curl_close($ch);

        return response($result);
    }

    function send_msg()
    {

    }
}
