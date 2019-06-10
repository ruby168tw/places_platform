<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SmscodeVerification extends Model
{
    protected $fillable = 
    [
        'phone', 'captcha', 'statuscode', 'error', 'msgid', 'smscode', 'type'
    ];

    // 算當天驗證次數
    public function count_times($phone, $type)
    {
        
        $count = DB::table('smscode_verifications')
        ->where('phone', $phone)
        ->where('type', $type)
        ->whereRaw("`created_at` BETWEEN CURRENT_DATE AND date_add(now(), interval 1 day)")
        ->count();

        return($count);
    }

    
    // 查詢"phone"與"smscode"是否正確
    public function phone_smscode_verification_result($phone, $smscode)
    {
        return SmscodeVerification::where('phone', $phone)
        ->where('smscode', $smscode)
        ->whereRaw("`created_at` BETWEEN CURRENT_DATE AND date_add(now(), interval 1 day)")
        ->orderBy('created_at', 'desc')
        ->first();
    }

    // 查詢資料是否已過期
    public function valid_time($phone, $smscode)
    {
        return SmscodeVerification::where('phone', $phone)
        ->where('smscode', $smscode)
        ->whereRaw(" created_at >= now()-interval 2 minute")
        ->first();
    }
}
