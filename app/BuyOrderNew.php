<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyOrderNew extends Model
{
    public $timestamps = false ; 
    protected $table = 'buy_order_news';
    protected $guarded = ['id'] ;

    public function Product()
    {
        return $this->belongsTo('App\Product');
    }
    public function Color()
    {
        return $this->belongsTo('App\Color');
    }
    public function Size()
    {
        return $this->belongsTo('App\Size');
    }
}
