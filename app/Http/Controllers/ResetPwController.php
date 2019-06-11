<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\User;
use App\SmscodeVerification;
use DB;

class ResetPwController extends Controller
{
    /**
     * to do
     * 1. code 失效時間審核
     */

    // show 重設密碼頁面
    public function showResetPwForm()
    {
        return view('auth.passwords.reset2');
    }


    // 重設密碼
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->validationErrorMessages());

        // 驗證資料格式是否正確(phone, smscode, password)
        if ($validator->passes())
        {
            $user = new User();

            if ($request->country == "taiwan")
            {
                // 驗證該組電話號碼是否存在
                if ($user->check_phone("886", $request->phone))
                {
                    $record = new SmscodeVerification();

                    // 驗證該組phone和smscode是否合法
                    if ($record->phone_smscode_verification_result("886", $request->phone, $request->smscode))
                    {
                        // 驗證時間是否合法
                        if ($record->valid_time("886", $request->phone, $request->smscode))
                        {
                            $user->update_password("886", $request->phone, $request->password);
                            echo "更改完成";
                        }
                        else 
                        {
                            echo "驗證碼已過期";    
                        }                     
                    }
                    else
                    {
                        echo "phone:".$request->phone."smscode:".$request->smscode."輸入資訊有誤，請再次確認";
                    }
                }
                else
                {
                    echo "查無此號碼，請再次確認";
                }
            }

            

        }
        else 
        {
         return redirect()->back()->withInput()->withErrors($validator->errors()); 
        }

       
       
    }

    public function test(Request $request)
    {
        $record = new SmscodeVerification();
        echo $record->phone_smscode_verification_result($request->phone, $request->smscode)->created_at;
    }

        
    

    // 表格驗證規則
    protected function rules()
    {
        return [
            'phone' => 'required|digits_between:9,10',
            'smscode' => 'required|digits:6',
            'password' => 'required|confirmed|min:4|max:20',
        ];
    }

    // 驗證錯誤訊息呈現內容
    protected function validationErrorMessages()
    {
        return [
            'phone.required' => '請填入手機號碼',
            'phone.digits_between:9,10' => '手機號碼格式有錯哦',
            'smscode.required' => '請填入簡訊驗證碼',
            'smscode.digits:6' =>'驗證碼格式有錯哦',
            'password.required' => '請填入新密碼',
            'password.confirmed' => '兩次密碼輸入不同哦',
            'password.min:8' => '密碼最少8位哦'
        ];
    }
}
