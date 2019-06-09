@extends('layouts.app')

{{--  reCAPTCHA  --}}
<script src="https://www.google.com/recaptcha/api.js?hl=zh-TW&render=explicit&onload=onReCaptchaLoad" async defer></script>

<script>
var captchaWidgetId;   
var onReCaptchaLoad = function() {   
  
            captchaWidgetId = grecaptcha.render( 'myCaptcha', {   
                'sitekey' : "{{ env('CAPTCHA_SITEKEY') }}",  // (required)   
                'theme' : 'light',  // (optional)   
                'callback': 'verifyCallback',  // (optional) executed when the user submits a successful response.
                'expired-callback':'',  // (optional) executed when the reCAPTCHA response expires and the user needs to re-verify.
                'error-callback':''  // (optional) executed when reCAPTCHA encounters an error (usually network connectivity) and cannot continue until connectivity is restored. 
                                     //            If you specify a function here, you are responsible for informing the user that they should retry.
            });   
};   

var verifyCallback = function( recaptcha ) 
{

    console.log(grecaptcha.getResponse(captchaWidgetId)); 

        // 發送 Ajax 查詢請求並處理
        var request = new XMLHttpRequest();
        request.open("POST", "{{ route('verifyRecaptcha')}}");
     
        // POST 參數須使用 send() 發送
        var data = "id=" + grecaptcha.getResponse(captchaWidgetId);
     
        // POST 請求必須設置表頭在 open() 下面，send() 上面
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        request.send(data);
     
        request.onreadystatechange = function() {
            // 伺服器請求完成
            if (request.readyState === 4) {
                // 伺服器回應成功
                if (request.status === 200) {
                    //將取得的結果做json解析
                    var verifyCaptchaResult = JSON.parse(request.responseText);
                    //取success的結果值
                    document.getElementById("result").innerHTML=verifyCaptchaResult.success;
                    //reCAPTCHA驗證成功
                    if (document.getElementById("result").innerHTML == "true")
                    {                        
                        sendMsg();
                    }
                    
                } 
                else 
                {
                    console.log("reCAPTCHA error");
                }
            }
        }
};

function sendMsg()
{
    // 發送 Ajax 查詢請求並處理
    var sending = new XMLHttpRequest();
    sending.open("POST", "{{ route('send_msg')}}");
 
    // POST 參數須使用 send() 發送
    var msgData = "id=" + grecaptcha.getResponse(captchaWidgetId) + "&phone=" + document.getElementById("phone").value;
    console.log(msgData);
 
    // POST 請求必須設置表頭在 open() 下面，send() 上面
    sending.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    sending.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
    sending.send(msgData);
 
    sending.onreadystatechange = function() 
    {
        // 伺服器請求完成
        if (sending.readyState === 4) 
        {
            // 伺服器回應成功
            if (sending.status === 200) 
            {
                console.log("msg OK");
            }
            else
            {
                console.log("msg fail");
            }
        }
    }
}

</script>



@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('重設密碼') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('手機號碼') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="phone" class="form-control" name="phone" value="{{ $phone ?? old('phone') }}" required autocomplete="phone" autofocus>

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="smscode" class="col-md-4 col-form-label text-md-right">{{ __('簡訊驗證碼') }}</label>

                            <div class="col-md-6">
                                <input id="smscode" type="smscode" class="form-control" name="smscode" value="{{ $smscode ?? old('smscode') }}" required >
                            </div>
                            <a class="btn btn-link" href="{{ route('callRecaptcha') }}">
                                {{ __('發送驗證碼') }}
                            </a>
                        </div>
                        
                        
                        

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('新密碼') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('確認新密碼') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        {{-- for getting reCAPTCHA response --}}
                        <div id="myCaptcha"></div> 
                        <div id="result"></div> 

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('設定完成') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
