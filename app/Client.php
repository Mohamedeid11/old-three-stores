<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public function city_info()
    {
        return $this->belongsTo('App\City', 'city');
    }

    public function orders()
    {
        return $this->hasMany('App\SellOrder', 'client');
    }    
    
    public function update_client_type()
    {
        $orders = $this->hasMany('App\SellOrder', 'client')->where('sell_orders.hide', 0)->count();
        if($orders > 1) 
        {
            $this->type = 1;
            $this->save();
        }
        else
        {
            $this->type = 0;
            $this->save();
        }
    }
    
      
    public function get_client_type()
    {
        return $this->type;
    }

}
