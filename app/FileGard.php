<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileGard extends Model
{
    //
    protected $guarded=[];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');

    }

    public function detailsFiles()
    {
        return $this->hasMany(DetailsFile::class, 'file_gard_id')->where('hide',0);
    }

}
