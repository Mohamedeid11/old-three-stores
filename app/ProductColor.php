<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    public function color_info()
    {
        return $this->belongsTo('App\Color', 'color');
    }
}
