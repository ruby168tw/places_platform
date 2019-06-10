@extends('layouts.app')

{{--  reCAPTCHA  start  --}}
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
     
        request.onreadystatechange = function() 
        {
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
    var msgData = "id=" + grecaptcha.getResponse(captchaWidgetId) + "&phone=" + document.getElementById("phone").value + "&type=register";
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
{{--  reCAPTCHA  end  --}}

{{--  顯示reCAPTCHA  --}}
function show(id)
{
    var cellphone = document.getElementById("phone").value;

    // 取得當日總驗證次數 若為非法模式，則得false
    var times = check_sending_times(cellphone).times;
    // 取得電話格式驗證結果，若通過則為true，若失敗則為false 
    var phone = check_sending_times(cellphone).phone;

    if (phone == true)
    {
        if (times < 6)
        {
            var o=document.getElementById(id);
            if( o.style.display == 'none' )
            {
                o.style.display='';
            }
            else
            {
                o.style.display='none';
            }
            
            console.log("未滿5次");
        }
        else
        {
            alert("今日驗證次數已使用完畢囉～(每日限驗證：5次)");
        }
    }
    else
    {
        var o=document.getElementById(id);
        o.style.display='none';
        alert("手機格式有誤");
    }

}

{{--  查詢驗證次數  --}}
function check_sending_times(cellphone)
{
    let xhr = new XMLHttpRequest();
    xhr.open('POST', "{{ route('check_sending_times') }}", false);

    try 
    {
        // POST 參數須使用 send() 發送
        var params = "phone=" + cellphone + "&type=register";
    
        // POST 請求必須設置表頭在 open() 下面，send() 上面
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        xhr.send(params);
    
        if (xhr.status != 200) 
        {
            alert(`Error ${xhr.status}: ${xhr.statusText}`);
        } 
        else 
        {
            // 解析回傳結果
            var timesResult = JSON.parse(xhr.response);
            
            // 將結果存入陣列
            var results = new Object();
            
                // 驗證次數
                results['times'] = timesResult.times;
                // 手機號碼合法性
                results['phone'] = timesResult.phone;
            
                return results;    
        }
    } 
    catch(err) 
    { // instead of onerror
        alert("Request failed");
    };
}

</script>

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('註冊') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('手機號碼') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">

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
                            <a class="btn btn-link"  onclick='show("myCaptcha")'>
                                {{ __('發送驗證碼') }}
                            </a>
                        </div>

                        {{-- for showing reCAPTCHA --}}
                        <div id="myCaptcha" style="display:none"></div> 
                        <div id="result" style="visibility:hidden"></div> 


                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('密碼') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('確認密碼') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('註冊') }}
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
