<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyOrder extends Model
{
    public function agent_info()
    {
        return $this->belongsTo('App\Agent', 'agent');
    }
    public function client_info()
    {
        return $this->belongsTo('App\Agent', 'agent');
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
        return $this->hasMany('App\BuyOrderItem', 'order')->orderBy('id');
    }
    public function itemsq()
    {
        return $this->hasMany('App\BuyOrderItem', 'order')->where('qty', '>', 0)->orderBy('id');
    }
    public function products($products)
    {
        $abc = array();
        $order = $this->id;
        for ($i = 0; $i < count($products); $i++)
        {
            $aa = BuyOrderItem::where('order', $order)->where('product', $products[$i])->first();
            $abc[] = $aa->id;
        }
        return BuyOrderItem::whereIn('id', $abc)->orderBy('id')->get();
    }
    public function items_grouped()
    {
        return $this->hasMany('App\BuyOrderItem', 'order')->distinct('product')->orderBy('id');
    }
    public function delivery_info()
    {
        return $this->belongsTo('App\Admin', 'delivered_by');
    }
    public function notes()
    {
        return $this->hasMany('App\BuyOrderNote', 'order')->orderBy('id');
    }
    public function time_lines()
    {
        return $this->hasMany('App\TimeLine', 'order')->where('time_lines.order_type', '=', 2)->orderBy('time_lines.id');
    }
}
