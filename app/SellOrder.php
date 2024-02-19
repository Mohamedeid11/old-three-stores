<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellOrder extends Model
{
    protected $guarded=[];
    public function client_info()
    {
        return $this->belongsTo('App\Client', 'client');
    }
    public function status_info()
    {
        return $this->belongsTo('App\OrderStatus', 'status');
    }
    public function city_info()
    {
        return $this->belongsTo('App\City', 'city');
    }
    public function items()
    {
        return $this->hasMany('App\SellOrderItem', 'order')->orderBy('id');
    }
    public function itemsq()
    {
        return $this->hasMany('App\SellOrderItem', 'order')->where('qty', '!=', 0)->orderBy('id');
    }
    public function delivery_info()
    {
        return $this->belongsTo('App\Admin', 'delivered_by');
    }
    public function moderator_info()
    {
        return $this->belongsTo('App\Admin', 'added_by');
    }
    public function notes()
    {
        return $this->hasMany('App\OrderNote', 'order')->orderBy('id');
    }
    public function notes_desc()
    {
        return $this->hasMany('App\OrderNote', 'order')->orderBy('id','desc');
    }
    public function time_lines()
    {
        return $this->hasMany('App\TimeLine', 'order','id')
            ->where('order_type', 1)
            ->orderBy('id');
    }
    public function fullfilment_checker()
    {
        return $this->hasMany('App\Fulfillment', 'order');
    }
    public function tags()
    {
        return $this->hasMany('App\SellOrderTag', 'order_id');
    }
    public function latestThreeNotes()
    {
        return $this->hasMany('App\OrderNote', 'order')->latest()->limit(3);
    }
}
