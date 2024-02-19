<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderNoteTag extends Model
{

    public function tag_info()
    {
        return $this->belongsTo('App\Tag', 'tag');
    }
}
