<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmscodeVerification extends Model
{
    protected $fillable = 
    [
        'phone', 'captcha', 'statuscode', 'error', 'msgid', 'smscode'
    ];
}
