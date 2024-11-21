<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    public function sender(){
        return $this->belongsTo(User::class,"sender_id");
    }
    public function recevier(){
        return $this->belongsTo(User::class,"recever_id");
    }
}