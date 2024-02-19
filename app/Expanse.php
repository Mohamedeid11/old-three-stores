<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expanse extends Model
{
    function cat_info()
    {
        return $this->belongsTo('App\ExpanseCategory', 'cat');
    }
}
