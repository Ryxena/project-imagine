<?php

namespace App\Models;

use Database\Factories\PenghunianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'kamar_id', 'tanggal_masuk', 'tanggal_checkout', 'last_kamar_id'])]

class Penghunian extends Model
{
    /** @use HasFactory<PenghunianFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
}
