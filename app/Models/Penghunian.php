<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id','kamar_id','tanggal_masuk','tanggal_checkout','last_kamar_id'])]

class Penghunian extends Model
{   
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function kamar(){
        return $this->belongsTo(Kamar::class);
    }
}
