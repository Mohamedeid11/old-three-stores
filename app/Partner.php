<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    function cat_info()
    {
        return $this->belongsTo('App\PartnerCategory', 'cat');
    }
}
