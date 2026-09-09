<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id','judul','deskripsi','image','status'])]
class Keluhan extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
}
