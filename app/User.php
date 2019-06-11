<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'password', 'countryCode'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];

    // 查詢該"phone"是否存在
    public function check_phone($phone)
    {
        return User::where('phone', $phone)->first();
    }

    // 更新使用者密碼
    public function update_password($phone, $password)
    {
        DB::table('users')
        ->where('phone', $phone)
        ->update(['password' => Hash::make($password), 'updated_at' => date('Y-m-d H:i:s')]);
    }

}
