<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CaptchaTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_verify_captcha($id="03AOLTBLRjMURwZMGBCNiirHzb2QxoWvWjXV0DbMBMQh8da3LQ076x6OirrBUr6ZUP0LHfZYdJsuViQSCIEvk1DFdypBQABRumCxCpRFCfXbJZcLvrSsjife-uLWHccK1-izLGG1UDslEGU4rLxRyDpF1NTupGI66zmS3t6UP87u7aHzBoCqWcHS_rMyYGmGApAIB0uULZbrhmBMX2QdQSThHxNgADIEOPBtPz0neDYlqPheYTmwsm8PTp8Nl3wrpac-cif5RMvkD_b_tfPSbuoqe_hbzbHn95w0IrVfKAyMteW4ahYN8jAiGiOe7POnZ8lgvoWGz5tjai")
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( array( "secret" => env('CAPTCHA_SECRET'), "response" => $id) ));
        //執行，並將結果存回
        $result = curl_exec($ch);
        //關閉連線
        curl_close($ch);

        $this->assertContains("success", $result);

    }
}
