<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeLine extends Model
{
    public function admin_info()
    {
        return $this->belongsTo('App\Admin', 'admin');
    }
    public function order_info(){
        return $this->belongsTo('App\SellOrder','order');
    }
}
