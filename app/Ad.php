<?php

namespace App;

use Codexshaper\WooCommerce\Models\Order;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    //
    protected $guarded=[];
    public function platforms(){
    return   $this->belongsToMany(OrderTag::class,'ad_platforms','ad_id','platform_id');
    }

    public function products(){
        return $this->belongsToMany(Product::class,'ad_products','ad_id','product_id');
    }
}
