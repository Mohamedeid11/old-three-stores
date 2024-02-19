<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    function sub_cats ()
    {
        return $this->hasMany('App\Category', 'cat')->where('hide', 0)->get();
    }
    
    function products ()
    {
        return $this->hasMany('App\Product', 'cat')->where('hide', 0)->where('discontinue', '=', 0)->get();
    }
}
