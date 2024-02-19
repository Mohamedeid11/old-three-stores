<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class  RuinedItemAdmins extends Model
{
    //
    protected $guarded=[];

    public function admin(){
        return $this->belongsTo(Admin::class,'added_by');
    }

}
