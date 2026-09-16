<?php

namespace App\Models;

use Database\Factories\TagihanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['penghunian_id', 'bulan_tagihan', 'jumlah'])]
class Tagihan extends Model
{
    /** @use HasFactory<TagihanFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $appends = ['status_pembayaran'];

    public function Penghunian(): BelongsTo
    {
        return $this->belongsTo(Penghunian::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Status pembayaran tagihan yang diturunkan dari status_verifikasi pembayaran.
     *
     * @return string lunas|menunggu_verifikasi|ditolak|belum_bayar
     */
    public function getStatusPembayaranAttribute(): string
    {
        $statuses = $this->pembayaran->pluck('status_verifikasi');

        if ($statuses->contains('success')) {
            return 'lunas';
        }

        if ($statuses->contains('pending')) {
            return 'menunggu_verifikasi';
        }

        if ($statuses->contains('failed')) {
            return 'ditolak';
        }

        return 'belum_bayar';
    }
}
