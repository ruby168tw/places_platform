<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SmscodeVerification;

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

    function send_sms(Request $request)
    {

        /** to do 
         * 驗證電話號碼格式:
         * 如果是9碼，開頭要為9
         * 如果是10碼，開頭要為09
         */
        $code = rand(111111,999999);
        $url = 'http://smsapi.mitake.com.tw/api/mtk/SmSend?'; 
        $url .= '&username='.env('MITAKE_USERNAME');
        $url .= '&password='.env('MITAKE_PASSWORD');
        $url .= '&dstaddr='.$request->phone;
        $url .= '&smbody='.urlencode($code);
        // $url .= '&clientid='.$request->phone; 
        $url .= '&CharsetURL=UTF-8';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($curl);
        curl_close($curl);
        $array_outputs = explode("\n", $output);
        print_r($array_outputs);
        /**
         * to do 2
         * 將phone, smscode
         * 存進password_resets
         */

        //  解析三竹系統的response
         foreach ($array_outputs as $array_output) 
         {
             if (preg_match("/statuscode/i", $array_output))
             {
                $statuscode = strchr($array_output, "="); // 取"="之後，包含"="的所有字串
                $statuscode = str_replace("=", "",$statuscode); //將"="去除
                $statuscode = trim($statuscode); //去除空白
             }

             if (preg_match("/msgid/i", $array_output))
             {
                $msgid = strchr($array_output, "="); // 取"="之後，包含"="的所有字串
                $msgid = str_replace("=", "",$msgid); //將"="去除
                $msgid = trim($msgid); //去除空白
             }

             if (preg_match("/Error/i", $array_output))
             {
                $error = strchr($array_output, "="); // 取"="之後，包含"="的所有字串
                $error = str_replace("=", "",$error); //將"="去除
                $error = trim($error); //去除空白
             }
         }

        //  存發送簡訊驗證碼紀錄
         if (empty($msgid))
         {
            \App\SmscodeVerification::create(['phone' => $request->phone, 'captcha' => $request->id, 'statuscode' => $statuscode, 'error' => $error, 'smscode' => $code]);
         }
         else
         {
            \App\SmscodeVerification::create(['phone' => $request->phone, 'captcha' => $request->id, 'statuscode' => $statuscode, 'msgid' => $msgid, 'smscode' => $code]);
         }

         

         
    }
}
