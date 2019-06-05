<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use User;
use password_reset;

class ResetPwController extends Controller
{
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
           if (User::where('phone', $request->phone)->where('phone', $request->phone)->first)
           {

           }
           $resetPwRecord = password_reset::firstOrCreate(['phone' => $request->phone, 'smscode' => $request->smscode]);

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
            'password' => 'required|confirmed|min:8',
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
