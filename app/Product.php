<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function colors()
    {
        return $this->hasMany('App\ProductColor', 'product');
    }
    public function sizes()
    {
        return $this->hasMany('App\ProductSize', 'product');
    }
    public function inventories()
    {
        return $this->hasMany('App\Inventory', 'product');
    }


    public function timeline()
    {
        return $this->hasMany('App\ProductTimeline', 'product');
    }

    public function sold_items()
    {
        return $this->hasMany('App\SellOrderItem', 'product')->where('qty', '>', 0)->where('hide', '=', 0);
    }

    
    public function bought_items()
    {
        return $this->hasMany('App\BuyOrderItem', 'product')->where('qty', '>', 0)->where('hide', '=', 0);
    }  
    public function images ()
    {
        return $this->hasMany('App\ProductImage', 'product');
    }
    public function cat_info ()
    {
        return $this->belongsTo('App\Category', 'cat');
    }

    public function sell_orders(){
        return $this->belongsToMany(SellOrder::class,'sell_order_items','product','order');
    }
    public function tags(){
        return $this->belongsToMany(TagGroup::class,'product_tags','product_id','tag_id');
    }


    public function sellOrdersWithAdDate($date){
        return $this->belongsToMany(SellOrder::class, 'sell_order_items', 'product', 'order')
            ->where(function ($query) use ($date) {
                $query->where('created_ad', '>=', $date . ' 00:00:00')
                    ->where('created_at', '<=', $date . ' 23:59:59')
                ->where('created_at', '!=', null);

            });
    }


}
