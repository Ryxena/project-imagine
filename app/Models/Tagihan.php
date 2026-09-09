<?php

namespace App\Models;
use App\Models\Penghunian;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['penghunian_id','total_tagihan','status_tagihan'])]
class Tagihan extends Model
{
    //
    public function Penghunian(){
        return $this->belongsTo(Penghunian::class);
    }
}
