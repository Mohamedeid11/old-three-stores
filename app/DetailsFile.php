<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailsFile extends Model
{
    //
    protected $guarded=[];
    public function inventory(){
        return $this->belongsTo(Inventory::class,'inventory_id');
    }
}
