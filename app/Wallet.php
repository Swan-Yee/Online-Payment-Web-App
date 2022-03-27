<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $guarded= [];

    public function User(){
        return $this->belongsTo(User::class,'user_id','id');
    }

}
