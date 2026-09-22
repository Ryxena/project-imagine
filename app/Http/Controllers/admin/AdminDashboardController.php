<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Keluhan;
use App\Models\Pembayaran;
use App\Models\Penghunian;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalKamar = Kamar::count();
        $kamarTerisi = Penghunian::whereNull('tanggal_checkout')
            ->whereNotNull('kamar_id')
            ->distinct('kamar_id')
            ->count('kamar_id');

        $penghuniAktif = Penghunian::whereNull('tanggal_checkout')->count();
        $penghuniBulanIni = Penghunian::whereNull('tanggal_checkout')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $tagihanBulanIni = Tagihan::where('bulan_tagihan', now()->format('Y-m'))
            ->with('pembayaran')
            ->get();
        $tagihanLunas = $tagihanBulanIni->filter(fn ($t) => $t->status_pembayaran === 'lunas')->count();

        $kpi = [
            'kamar_terisi' => $kamarTerisi,
            'kamar_total' => $totalKamar,
            'penghuni_aktif' => $penghuniAktif,
            'penghuni_bulan_ini' => $penghuniBulanIni,
            'tagihan_lunas' => $tagihanLunas,
            'tagihan_total' => $tagihanBulanIni->count(),
        ];

        $perluTindakan = [];

        $verifikasiPendingCount = Pembayaran::where('status_verifikasi', 'pending')->count();
        if ($verifikasiPendingCount > 0) {
            $perluTindakan[] = [
                'type' => 'verifikasi',
                'label' => 'Verifikasi Pembayaran',
                'meta' => "{$verifikasiPendingCount} bukti transfer baru diunggah.",
                'count' => $verifikasiPendingCount,
                'route' => 'admin.verifikasipembayaran.index',
            ];
        }

        $telatCount = Tagihan::with('pembayaran')->get()
            ->filter(fn ($t) => $t->status_pembayaran === 'belum_bayar' && $t->created_at->lte(now()->subDays(3)))
            ->count();
        if ($telatCount > 0) {
            $perluTindakan[] = [
                'type' => 'telat',
                'label' => 'Penghuni Telat Bayar',
                'meta' => "{$telatCount} penghuni melewati jatuh tempo (>3 hari).",
                'route' => 'admin.tagihan.index',
            ];
        }

        $keluhanAktifCount = Keluhan::whereIn('status', ['pending', 'process'])->count();
        if ($keluhanAktifCount > 0) {
            $keluhanTerbaru = Keluhan::whereIn('status', ['pending', 'process'])->latest()->first();
            $perluTindakan[] = [
                'type' => 'keluhan',
                'label' => 'Keluhan Aktif',
                'meta' => $keluhanAktifCount === 1
                    ? "1 keluhan baru: \"{$keluhanTerbaru->judul}\"."
                    : "{$keluhanAktifCount} keluhan menunggu ditindaklanjuti.",
                'route' => 'admin.keluhan.index',
            ];
        }

        $kamarKosongCount = $totalKamar - $kamarTerisi;
        if ($kamarKosongCount > 0) {
            $perluTindakan[] = [
                'type' => 'kamar_kosong',
                'label' => 'Kamar Kosong',
                'meta' => "{$kamarKosongCount} kamar siap disewakan.",
                'route' => 'admin.kamar.index',
            ];
        }

        $recentPembayaran = Pembayaran::with('tagihan.penghunian.user', 'tagihan.penghunian.kamar')
            ->latest()->take(3)->get()
            ->map(fn ($p) => [
                'name' => $p->tagihan->penghunian->user->name ?? 'Penghuni',
                'action' => 'mengunggah bukti transfer untuk kamar '.($p->tagihan->penghunian->kamar->nomor_kamar ?? '-').'.',
                'waktu' => $p->created_at,
            ]);

        $recentKeluhan = Keluhan::with('user')->latest()->take(3)->get()
            ->map(fn ($k) => [
                'name' => $k->user->name ?? 'Penghuni',
                'action' => 'membuat keluhan "'.$k->judul.'".',
                'waktu' => $k->created_at,
            ]);

        $recentPenghunian = Penghunian::with('user')->latest()->take(3)->get()
            ->map(fn ($p) => [
                'name' => $p->user->name ?? 'Penghuni',
                'action' => 'terdaftar sebagai penghuni baru.',
                'waktu' => $p->created_at,
            ]);

        $aktivitas = $recentPembayaran
            ->concat($recentKeluhan)
            ->concat($recentPenghunian)
            ->sortByDesc('waktu')
            ->take(5)
            ->map(fn ($item) => [
                'name' => $item['name'],
                'action' => $item['action'],
                'waktu' => $item['waktu']->locale('id')->diffForHumans(),
            ])
            ->values()
            ->all();

        return view('admin.dashboard.index', compact('kpi', 'perluTindakan', 'aktivitas'));
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
