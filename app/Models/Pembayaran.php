<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tagihan_id','bukti_pembayaran','status_verifikasi'])]
class Pembayaran extends Model
{
    public function tagihan(){
        return $this->belongsTo(Tagihan::class);
    }
}
