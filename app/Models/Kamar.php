<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nomor_kamar', 'tipe_kamar', 'harga', 'deskripsi'])]
class Kamar extends Model
{
    public function Penghunian(){
        return $this->hasMany(Penghunian::class);
    }
}
