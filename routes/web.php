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

// 發送簡訊(三竹)
Route::post('/msg', 'CaptchaController@execute_sneding_and_record')->name('send_msg');

