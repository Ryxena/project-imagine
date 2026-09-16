<?php

namespace App\Models;

use Database\Factories\PengumumanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['admin_id', 'judul', 'deskripsi', 'tanggal_publish', 'type'])]
class Pengumuman extends Model
{
    /** @use HasFactory<PengumumanFactory> */
    use HasFactory;

    /**
     * Tabel terkait model ini, karena plural otomatis ("pengumumen") tidak sesuai.
     */
    protected $table = 'pengumuman';

    //
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
