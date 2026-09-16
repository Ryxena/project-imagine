<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Keluhan;
use App\Models\Notification;
use App\Models\Pembayaran;
use App\Models\Penghunian;
use App\Models\Pengumuman;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Password yang dipakai untuk semua akun seeder.
     */
    private const PASSWORD = 'password';

    /**
     * Seed data untuk menguji seluruh route admin dan penghuni.
     */
    public function run(): void
    {
        // ===== USERS =====
        // Admin: admin@imagine.test / password
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Kos',
            'email' => 'admin@imagine.test',
        ]);

        // Penghuni aktif: {nama-lower}@imagine.test / password
        $penghuniAktif = collect([
            ['name' => 'Budi Santoso', 'email' => 'budi@imagine.test'],
            ['name' => 'Siti Rahma', 'email' => 'siti@imagine.test'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@imagine.test'],
        ])->map(fn (array $data) => User::factory()->penghuni()->create($data));

        // Penghuni tanpa kamar, untuk menguji route assignkamar
        $penghuniTanpaKamar = User::factory()->penghuni()->create([
            'name' => 'Rina Melati',
            'email' => 'rina@imagine.test',
        ]);

        // Penghuni yang sudah checkout
        $penghuniCheckout = User::factory()->penghuni()->create([
            'name' => 'Dedi Kurnia',
            'email' => 'dedi@imagine.test',
        ]);

        // Akun non-aktif, untuk menguji penolakan login
        User::factory()->penghuni()->nonaktif()->create([
            'name' => 'Non Aktif',
            'email' => 'nonaktif@imagine.test',
        ]);

        // ===== KAMAR =====
        $kamars = collect([
            ['nomor_kamar' => 'A101', 'tipe_kamar' => 'standar', 'harga' => 750000, 'deskripsi' => 'Kamar standar lantai 1, kamar mandi luar.'],
            ['nomor_kamar' => 'A102', 'tipe_kamar' => 'standar', 'harga' => 750000, 'deskripsi' => 'Kamar standar lantai 1, dekat parkiran.'],
            ['nomor_kamar' => 'B201', 'tipe_kamar' => 'deluxe', 'harga' => 1250000, 'deskripsi' => 'Kamar deluxe lantai 2, kamar mandi dalam dan AC.'],
            ['nomor_kamar' => 'B202', 'tipe_kamar' => 'deluxe', 'harga' => 1250000, 'deskripsi' => 'Kamar deluxe lantai 2, dengan balkon.'],
            ['nomor_kamar' => 'C301', 'tipe_kamar' => 'vip', 'harga' => 2000000, 'deskripsi' => 'Kamar VIP lantai 3, full furniture dan AC.'],
        ])->map(fn (array $data) => Kamar::create($data));

        // ===== PENGHUNIAN =====
        // Tiga penghuni aktif menempati kamar A101, A102, B201
        $penghunianAktif = $penghuniAktif->map(function (User $user, int $i) use ($kamars) {
            return Penghunian::create([
                'user_id' => $user->id,
                'kamar_id' => $kamars[$i]->id,
                'last_kamar_id' => $kamars[$i]->id,
                'tanggal_masuk' => now()->subMonths(3)->startOfMonth()->toDateString(),
            ]);
        });

        // Penghunian tanpa kamar, siap di-assign lewat route assignkamar
        $penghunianTanpaKamar = Penghunian::create([
            'user_id' => $penghuniTanpaKamar->id,
        ]);

        // Penghunian yang sudah checkout, kamar C301 kembali tersedia
        Penghunian::create([
            'user_id' => $penghuniCheckout->id,
            'kamar_id' => null,
            'last_kamar_id' => $kamars[4]->id,
            'tanggal_masuk' => now()->subYear()->startOfMonth()->toDateString(),
            'tanggal_checkout' => now()->subMonths(3)->toDateString(),
        ]);

        // ===== PENGUMUMAN =====
        collect([
            ['judul' => 'Jadwal Pembayaran Bulanan', 'type' => 'umum', 'deskripsi' => 'Pembayaran tagihan dilakukan paling lambat tanggal 10 setiap bulannya.'],
            ['judul' => 'Perbaikan Air Bertahap', 'type' => 'informasi', 'deskripsi' => 'Perbaikan pipa air dilakukan mulai Senin depan, air mengalir terputus antara pukul 09.00-12.00.'],
            ['judul' => 'Dilarang Membawa Tamu Menginap', 'type' => 'penting', 'deskripsi' => 'Tamu yang menginap lebih dari satu malam wajib melapor ke pengelola.'],
        ])->each(fn (array $data) => Pengumuman::create([
            ...$data,
            'admin_id' => $admin->id,
            'tanggal_publish' => now()->toDateString(),
        ]));

        // ===== KELUHAN =====
        collect([
            ['user' => $penghuniAktif[0], 'judul' => 'Lampu kamar mati', 'deskripsi' => 'Lampu kamar A101 berkedip lalu mati total sejak kemarin malam.', 'status' => 'process'],
            ['user' => $penghuniAktif[1], 'judul' => 'Air kamar mandi lambat', 'deskripsi' => 'Air di kamar mandi lantai 1 mengalir sangat lambat pagi hari.', 'status' => 'pending'],
            ['user' => $penghuniAktif[2], 'judul' => 'WiFi sering putus', 'deskripsi' => 'Koneksi WiFi di kamar B201 sering terputus pada malam hari.', 'status' => 'resolved'],
        ])->each(fn (array $data) => Keluhan::create([
            'user_id' => $data['user']->id,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'],
            'status' => $data['status'],
        ]));

        // ===== TAGIHAN + PEMBAYARAN + NOTIFIKASI =====
        $bulanIni = now()->format('Y-m');
        $bulanLalu = now()->subMonth()->format('Y-m');

        // Budi: tagihan bulan lalu sudah lunas, tagihan bulan ini belum bayar
        $tagihanLunas = Tagihan::create([
            'penghunian_id' => $penghunianAktif[0]->id,
            'bulan_tagihan' => $bulanLalu,
            'jumlah' => $kamars[0]->harga,
        ]);

        Pembayaran::create([
            'tagihan_id' => $tagihanLunas->id,
            'bukti_pembayaran' => 'pembayaran/bukti/seed-budi-lunas.png',
            'status_verifikasi' => 'success',
            'tanggal_pembayaran' => now()->subMonth()->setDay(5)->toDateString(),
        ]);

        Notification::create([
            'user_id' => $penghuniAktif[0]->id,
            'judul' => 'Pembayaran Diterima',
            'pesan' => "Pembayaran tagihan bulan {$bulanLalu} telah diverifikasi. Terima kasih.",
            'tipe' => 'pembayaran',
            'dibaca' => true,
        ]);

        $tagihanBaru = Tagihan::create([
            'penghunian_id' => $penghunianAktif[0]->id,
            'bulan_tagihan' => $bulanIni,
            'jumlah' => $kamars[0]->harga,
        ]);

        Notification::create([
            'user_id' => $penghuniAktif[0]->id,
            'judul' => 'Tagihan Baru',
            'pesan' => sprintf(
                'Tagihan untuk bulan %s sebesar Rp %s telah dibuat.',
                $bulanIni,
                number_format((float) $tagihanBaru->jumlah, 0, ',', '.')
            ),
            'tipe' => 'tagihan',
        ]);

        // Siti: tagihan bulan ini menunggu verifikasi admin
        $tagihanPending = Tagihan::create([
            'penghunian_id' => $penghunianAktif[1]->id,
            'bulan_tagihan' => $bulanIni,
            'jumlah' => $kamars[1]->harga,
        ]);

        Pembayaran::create([
            'tagihan_id' => $tagihanPending->id,
            'bukti_pembayaran' => 'pembayaran/bukti/seed-siti-pending.png',
            'status_verifikasi' => 'pending',
            'tanggal_pembayaran' => now()->setDay(3)->toDateString(),
        ]);

        Notification::create([
            'user_id' => $admin->id,
            'judul' => 'Pembayaran Baru',
            'pesan' => "Siti Rahma mengirim bukti pembayaran untuk tagihan bulan {$bulanIni}. Menunggu verifikasi.",
            'tipe' => 'pembayaran',
        ]);

        // Andi: tagihan bulan lalu ditolak, boleh kirim ulang bukti
        $tagihanDitolak = Tagihan::create([
            'penghunian_id' => $penghunianAktif[2]->id,
            'bulan_tagihan' => $bulanLalu,
            'jumlah' => $kamars[2]->harga,
        ]);

        Pembayaran::create([
            'tagihan_id' => $tagihanDitolak->id,
            'bukti_pembayaran' => 'pembayaran/bukti/seed-andi-ditolak.png',
            'status_verifikasi' => 'failed',
            'alasan_penolakan' => 'Bukti pembayaran tidak terbaca.',
            'tanggal_pembayaran' => now()->subMonth()->setDay(8)->toDateString(),
        ]);

        Notification::create([
            'user_id' => $penghuniAktif[2]->id,
            'judul' => 'Pembayaran Ditolak',
            'pesan' => "Pembayaran tagihan bulan {$bulanLalu} ditolak. Alasan: Bukti pembayaran tidak terbaca.",
            'tipe' => 'pembayaran',
        ]);

        // Tagihan bulan ini untuk Siti dan Andi juga, biar semua punya tagihan berjalan
        $penghunianAktif->skip(1)->each(function (Penghunian $penghunian) use ($bulanIni) {
            Tagihan::create([
                'penghunian_id' => $penghunian->id,
                'bulan_tagihan' => $bulanIni,
                'jumlah' => $penghunian->kamar?->harga ?? 0,
            ]);
        });
    }
}
