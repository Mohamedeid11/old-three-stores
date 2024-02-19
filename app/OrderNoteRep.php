<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderNoteRep extends Model
{
    
    public function rep_info()
    {
        return $this->belongsTo('App\Admin', 'rep');
    }
}
