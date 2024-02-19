<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    public function agent_balances(){
        return $this->hasMany(AgentBalance::class,'agent_id');
    }
}
