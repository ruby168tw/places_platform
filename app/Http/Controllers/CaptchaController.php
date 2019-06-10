<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmscodeVerification;
use User;
use Validator;

class CaptchaController extends Controller
{

    // 執行驗證碼發送及記錄，供前端使用by ajax，在reCAPTCHA驗證完成後
    public function execute_sneding_and_record(Request $request)
    {
        // 當"phone"存在而且不為空字串
        if ($request->has('phone'))
        {
            $mitake_response = $this->send_sms($request);
            $mitake_results = $this->parse_msg_response($mitake_response);

            if ($request->type == "reset")
            {
                $this->save_into_db($mitake_results, $mitake_response, "reset");
            }
            else if ($request->type == "register")
            {
                $this->save_into_db($mitake_results, $mitake_response, "register");
            }
            
        }
        else return false;
    }

    
    // 取得reCAPTCHA驗證結果，供前端使用by ajax
    public function verify_captcha(Request $request)
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

    // call 三竹API發送otp簡訊
    public function send_sms(Request $request)
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

        // 放phone和code和captchaid進array_outputs
        $array_outputs['phone'] = $request->phone;
        $array_outputs['smscode'] = $code;
        $array_outputs['captcha'] = $request->id;
        
        return $array_outputs;
        
    }


    //  解析三竹系統的response
    public function parse_msg_response($array_outputs)
    {
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

        if (empty($msgid))
        {
            $mitake_results = 
            [
                "statuscode" => $statuscode,
                "error" => $error
            ];
        }
        else 
        {
            $mitake_results = 
            [
                "statuscode" => $statuscode,
                "msgid" => $msgid
            ];    
        }
        return $mitake_results;
    }

    //  存發送簡訊驗證碼紀錄
    public function save_into_db($mitake_results, $params, $type)
    {
        if (empty($mitake_results['msgid']))
        {
            \App\SmscodeVerification::create(['phone' => $params['phone'], 'captcha' => $params['captcha'], 'statuscode' => $mitake_results['statuscode'], 'error' => $mitake_results['error'], 'smscode' => $params['smscode'], 'type' => $type]);
        }
        else
        {
            \App\SmscodeVerification::create(['phone' => $params['phone'], 'captcha' => $params['captcha'], 'statuscode' => $mitake_results['statuscode'], 'msgid' => $mitake_results['msgid'], 'smscode' => $params['smscode'], 'type' => $type]);
        }        
    }

    // 查詢當日發送驗證次數
    public function check_sending_times(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|digits_between:9,10'], ['phone.required' => '請填入手機號碼','phone.digits_between:9,10' => '手機號碼格式有錯哦']);
        
        if ($validator->passes())
        {
        // 算當天驗證次數
        $verification = new SmscodeVerification();
        $count = $verification->count_times($request->phone, $request->type);
        return response()->json(["times" => $count, "phone" =>true]);
        }
        else 
        {
            return response()->json(["times" => false, "phone" =>false]);
        }
    }

}
