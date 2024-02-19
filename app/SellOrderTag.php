<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellOrderTag extends Model
{
    public function tag()
    {
        return $this->belongsTo('App\OrderTag', 'tag_id');
    }
}
