<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderNote extends Model
{
    protected $guarded=[];
    public function order_info()
    {
        return $this->belongsTo('App\SellOrder', 'order');
    }

    public function admin_info()
    {
        return $this->belongsTo('App\Admin', 'added_by');
    }
    
    
    public function reps()
    {
        return $this->hasMany('App\OrderNoteRep', 'note');
    }

    public function tags()
    {
        return $this->hasMany('App\OrderNoteTag', 'note');
    }



}
