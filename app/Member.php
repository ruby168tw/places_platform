<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    // 屬於該用戶的公司
    public function organizations()
    {
        return $this->belongsToMany('App\Organization')->withTimestamps();
    }
    
}
