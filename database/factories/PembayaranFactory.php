<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tagihan_id' => Tagihan::factory(),
            'bukti_pembayaran' => 'pembayaran/bukti/'.fake()->uuid().'.png',
            'status_verifikasi' => 'pending',
            'tanggal_pembayaran' => now()->toDateString(),
        ];
    }

    /**
     * Pembayaran yang menunggu verifikasi.
     */
    public function pending(): static
    {
        return $this->state(fn () => ['status_verifikasi' => 'pending']);
    }

    /**
     * Pembayaran yang sudah diverifikasi dan diterima.
     */
    public function success(): static
    {
        return $this->state(fn () => ['status_verifikasi' => 'success']);
    }

    /**
     * Pembayaran yang sudah diverifikasi dan ditolak.
     */
    public function failed(): static
    {
        return $this->state(fn () => [
            'status_verifikasi' => 'failed',
            'alasan_penolakan' => 'Bukti pembayaran tidak terbaca.',
        ]);
    }
}
