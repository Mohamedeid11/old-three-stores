<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RuinedItem extends Model
{
    protected $guarded=[];
    //
    public function destroyer(){
        return $this->hasMany(RuinedItemAdmins::class,'ruined_item_id')->take(5)->orderBy('id','DESC');
    }
}
