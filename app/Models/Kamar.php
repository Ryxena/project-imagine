<?php

namespace App\Models;

use Database\Factories\KamarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nomor_kamar', 'tipe_kamar', 'harga', 'deskripsi'])]
class Kamar extends Model
{
    /** @use HasFactory<KamarFactory> */
    use HasFactory;

    public function Penghunian()
    {
        return $this->hasMany(Penghunian::class);
    }
}
