<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['admin_id','judul','deskripsi','tanggal_publish','type'])]
class Pengumuman extends Model
{
    //
    public function admin(){
        return $this->belongsTo(User::class, 'admin_id');
    }
}
