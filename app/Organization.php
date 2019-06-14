<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    // 屬於該公司的用戶
    public function members()
    {
        return $this->belongsToMany('App\Member')->withTimestamps();
    }
    
    public function search($query)
    {
        $result =  Organization::where('name', $query)->first();
    }
}
