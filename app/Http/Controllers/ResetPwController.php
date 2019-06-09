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
       
       if ($validator->passes())
       {
           $user = new User();

           if ($user->check_phone($request->phone))
           {
               $record = new SmscodeVerification();


               if ($record->phone_smscode_verification_result($request->phone, $request->smscode))
               {
                    $user->update_password($request->phone, $request->password);
                    echo "更改完成";
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
       else 
       {
        return redirect()->back()->withInput()->withErrors($validator->errors()); 
       }

       
       
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

    //重設成功
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
                            ->with('status', trans($response));
    }

    //重設失敗
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                    ->withInput($request->only('phone'))
                    ->withErrors(['phone' => trans($response)]);
    }
}
