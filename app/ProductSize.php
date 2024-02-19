<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    public function size_info()
    {
        return $this->belongsTo('App\Size', 'size');
    }
}
