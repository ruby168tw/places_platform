<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// 驗證reCAPTCHA
Route::post('/recaptcha', 'CaptchaController@verify_captcha')->name('verifyRecaptcha');

// 發送簡訊(三竹)及紀錄發送狀況
Route::post('/msg', 'CaptchaController@execute_sneding_and_record')->name('send_msg');

// 呼叫reCAPTCHA跳出
Route::get('/recaptcha', 'CaptchaController@verify_click_sending_quality')->name('callRecaptcha');


