<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fulfillment extends Model
{
    public function item_info ()
    {
        return $this->belongsTo('App\SellOrderItem', 'item');
    }
}
