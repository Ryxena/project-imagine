@extends('layout.penghuni.PenghuniDashboardLayout')

@php
    $hour = now('Asia/Jakarta')->hour;
    $greeting = match (true) {
        $hour < 11 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };
@endphp

@section('page-title')
    @include('partial.tenant-status-banner')
    <span class="flex items-center gap-3">
        <span>{{ $greeting }}, {{ auth()->user()->name }}!</span>
        <span class="hidden sm:inline-flex p-2 rounded-2xl text-sage-700 animate-bounce duration-1000">
            👋
        </span>
    </span>
@endsection
@section('page-subtitle', 'Waktunya rehat sambil pantau kenyamanan kost.')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-4 animate-in">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-white via-cream-50/60 to-sage-50/50 rounded-3xl border border-cream-200/80 shadow-md p-6 lg:p-7">
            <div class="absolute -top-16 -right-16 w-48 h-48 bg-sage-300/20 rounded-full blur-3xl pointer-events-none"></div>
            <div
                class="absolute -bottom-16 -left-16 w-48 h-48 bg-terracotta-200/15 rounded-full blur-3xl pointer-events-none">
            </div>

            @if ($tagihanHero)
                @php
                    $bulanLabel = \Carbon\Carbon::createFromFormat(
                        'Y-m',
                        $tagihanHero->bulan_tagihan,
                    )->translatedFormat('F Y');

                    $ditolakHero = $tagihanHero->pembayaran
                        ->sortByDesc('created_at')
                        ->firstWhere('status_verifikasi', 'failed');

                    [$badgeClass, $badgeLabel, $iconBg] = match ($tagihanHero->status_pembayaran) {
                        'menunggu_verifikasi' => [
                            'bg-amber-100 text-amber-700 border border-amber-200',
                            'Menunggu Verifikasi',
                            'bg-amber-100 text-amber-600 ring-4 ring-amber-50',
                        ],
                        'ditolak' => [
                            'bg-terracotta-100 text-terracotta-700 border border-terracotta-200',
                            'Ditolak',
                            'bg-terracotta-100 text-terracotta-600 ring-4 ring-terracotta-50',
                        ],
                        default => [
                            'bg-terracotta-100 text-terracotta-700 border border-terracotta-200',
                            'Belum Bayar',
                            'bg-terracotta-100 text-terracotta-600 ring-4 ring-terracotta-50',
                        ],
                    };
                @endphp

                <div class="relative z-10">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div
                                class="w-12 h-12 rounded-2xl {{ $iconBg }} flex items-center justify-center shrink-0 shadow-sm transition-transform duration-300 hover:scale-105">
                                @include('partial.icons.warning', ['class' => 'w-6 h-6'])
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-cream-500 tracking-wider">Status Tagihan</p>
                                <p class="text-base font-extrabold text-cream-900 truncate">Periode {{ $bulanLabel }}</p>
                            </div>
                        </div>
                        <span
                            class="{{ $badgeClass }} text-[10px] sm:text-[11px] font-extrabold px-3 py-1.5 rounded-full shrink-0 shadow-2xs text-center leading-tight">{{ $badgeLabel }}</span>
                    </div>

                    <div class="mt-6 flex items-baseline gap-2">
                        <span class="text-3xl lg:text-4xl font-extrabold text-cream-900 tracking-tight">Rp
                            {{ number_format($tagihanHero->jumlah, 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-1 text-xs text-cream-600 font-medium flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-sage-500 shrink-0"></span>
                        <span class="truncate">Kamar {{ $tagihanHero->penghunian->kamar->nomor_kamar ?? '-' }} &bull; Belum
                            lunas</span>
                    </p>

                    @if ($tagihanHero->status_pembayaran === 'ditolak' && $ditolakHero)
                        <div
                            class="mt-4 bg-terracotta-50/95 backdrop-blur-sm border border-terracotta-200 text-terracotta-800 text-xs rounded-2xl p-4 shadow-2xs">
                            <p class="font-bold flex items-center gap-2 mb-1 text-terracotta-900">
                                @include('partial.icons.warning', [
                                    'class' => 'w-4 h-4 text-terracotta-600 shrink-0',
                                ])
                                Alasan Pembayaran Ditolak:
                            </p>
                            <p class="leading-relaxed pl-6 text-terracotta-700">{{ $ditolakHero->alasan_penolakan }}</p>
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col sm:flex-row gap-2.5 pt-5 border-t border-cream-200/60">
                        @if ($tagihanHero->status_pembayaran !== 'menunggu_verifikasi')
                            <a href="{{ route('penghuni.tagihan.index') }}"
                                class="w-full sm:w-auto text-center flex items-center justify-center gap-2 bg-sage-700 text-white text-xs font-semibold px-5 py-3 rounded-xl hover:bg-sage-800 hover:shadow-lg active:scale-[0.98] transition-all duration-150 ease-out shadow-md shadow-sage-700/20">
                                @include('partial.icons.receipt', ['class' => 'w-4 h-4'])
                                Upload Bukti Pembayaran
                            </a>
                        @endif
                        <a href="{{ route('penghuni.tagihan.index') }}"
                            class="w-full sm:w-auto text-center flex items-center justify-center gap-2 bg-white/80 backdrop-blur-sm border border-cream-300 text-cream-700 text-xs font-semibold px-5 py-3 rounded-xl hover:bg-white hover:border-cream-400 transition-all duration-150 shadow-2xs">
                            Rincian Tagihan
                        </a>
                    </div>
                </div>
            @else
                <div class="relative z-10 flex flex-col items-center justify-center py-8 text-center">
                    @if (in_array($tenantStatus, ['unassigned', 'no_record']))
                        <div
                            class="w-16 h-16 rounded-3xl bg-cream-100 text-cream-600 ring-8 ring-cream-50 flex items-center justify-center mb-4 shadow-sm">
                            @include('partial.icons.receipt', ['class' => 'w-8 h-8'])
                        </div>
                        <p class="text-lg font-extrabold text-cream-900">Belum Ada Tagihan</p>
                        <p class="mt-1 text-xs text-cream-600 max-w-sm leading-relaxed">Tagihan akan muncul setelah Anda
                            ditempatkan ke kamar.</p>
                    @elseif ($tenantStatus === 'checked_out')
                        <div
                            class="w-16 h-16 rounded-3xl bg-cream-100 text-cream-600 ring-8 ring-cream-50 flex items-center justify-center mb-4 shadow-sm">
                            @include('partial.icons.receipt', ['class' => 'w-8 h-8'])
                        </div>
                        <p class="text-lg font-extrabold text-cream-900">Tidak Ada Tagihan Aktif</p>
                        <p class="mt-1 text-xs text-cream-600 max-w-sm leading-relaxed">Masa tinggal Anda sudah berakhir,
                            sehingga tidak ada tagihan berjalan.</p>
                    @else
                        @php
                            $hasPaidHistory = \App\Models\Tagihan::whereHas(
                                'penghunian',
                                fn($q) => $q->where('user_id', auth()->id()),
                            )
                                ->where('status_pembayaran', 'lunas')
                                ->exists();
                        @endphp

                        @if ($hasPaidHistory)
                            <div
                                class="w-16 h-16 rounded-3xl bg-sage-100 text-sage-700 ring-8 ring-sage-50 flex items-center justify-center mb-4 shadow-sm">
                                @include('partial.icons.shield', ['class' => 'w-8 h-8'])
                            </div>
                            <p class="text-lg font-extrabold text-sage-900">Semua Tagihan Sudah Lunas!</p>
                            <p class="mt-1 text-xs text-cream-600 max-w-sm leading-relaxed">Terima kasih sudah membayar
                                tepat
                                waktu. Kamu bisa beristirahat dengan tenang tanpa ada tanggungan.</p>
                        @else
                            <div
                                class="w-16 h-16 rounded-3xl bg-sage-100 text-sage-700 ring-8 ring-sage-50 flex items-center justify-center mb-4 shadow-sm">
                                @include('partial.icons.receipt', ['class' => 'w-8 h-8'])
                            </div>
                            <p class="text-lg font-extrabold text-sage-900">Belum Ada Tagihan Bulan Ini</p>
                            <p class="mt-1 text-xs text-cream-600 max-w-sm leading-relaxed">Belum ada tagihan aktif atau tagihan baru dari pengelola. Santai dulu!</p>
                        @endif
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-4">
            @if ($keluhanAktifCount > 0)
                <a href="{{ route('penghuni.keluhan.index') }}"
                    class="group relative overflow-hidden block bg-white rounded-2xl border border-cream-200/80 shadow-md p-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 ease-out">

                    <div class="flex items-center gap-3.5 relative z-10">
                        <div
                            class="w-11 h-11 rounded-2xl bg-terracotta-100 text-terracotta-600 ring-4 ring-terracotta-50 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-110 group-hover:bg-terracotta-500 group-hover:text-white transition-all duration-300">
                            @include('partial.icons.wrench', ['class' => 'w-5 h-5'])
                        </div>
                        <div>
                            <p
                                class="text-2xl font-extrabold text-cream-900 tracking-tight group-hover:text-terracotta-700 transition-colors duration-200">
                                {{ $keluhanAktifCount }}</p>
                            <p class="text-xs font-medium text-cream-600">Keluhan Aktif &bull; <span
                                    class="text-terracotta-600 group-hover:underline">Pantau &rarr;</span></p>
                        </div>
                    </div>
                </a>
            @else
                <div class="relative flex items-center py-2">
                    <div class="relative z-20 shrink-0 -mr-6 -translate-y-12">
                        <div
                            class="w-11 h-11 rounded-full
                       bg-terracotta-100
                       text-terracotta-700
                       ring-[5px] ring-cream-50
                       shadow-[0_10px_24px_rgba(0,0,0,0.12)]
                       flex items-center justify-center transition-all duration-300">

                            @include('partial.icons.wrench', ['class' => 'w-6 h-6'])

                        </div>
                    </div>

                    <div
                        class="flex-1 bg-white border border-terracotta-100 rounded-tr-[40px] rounded-br-[40px] rounded-bl-[40px] rounded-tl-none py-3.5 pl-10 pr-5 shadow-[0_8px_24px_rgba(0,0,0,0.08)]">
                        <p class="text-base font-semibold text-cream-900">
                            Ada kendala di kamar atau kos?
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-cream-600">
                            Jangan ragu untuk
                            <a href="{{ route('penghuni.keluhan.index') }}"
                                class="font-semibold text-terracotta-700 underline underline-offset-2 hover:text-terracotta-800 transition-colors">
                                sampaikan keluhanmu
                            </a>
                            biar tim teknisi bisa segera bantu perbaiki.
                        </p>

                    </div>

                </div>
            @endif

            @if ($pengumumanTerbaru)
                <a href="{{ route('penghuni.pengumuman.index') }}"
                    class="group relative overflow-hidden block rounded-2xl shadow-sm p-5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 ease-out"
                    style="background-color: #cce4c4; border: 1px solid #b5d5b5;">

                    <div class="relative z-10">
                        <p class="flex items-center justify-between text-[13px] font-bold tracking-wide"
                            style="color: #234223;">
                            <span class="flex items-center gap-2">
                                @include('partial.icons.megaphone', [
                                    'class' =>
                                        'w-4 h-4 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-12',
                                    'style' => 'color: #386138;',
                                ])
                                Pengumuman Terbaru
                            </span>
                            <span
                                class="text-[10px] font-bold bg-white/60 px-2 py-0.5 rounded-full text-[#386138]">Baru</span>
                        </p>

                        <p class="mt-3 text-[13px] leading-relaxed line-clamp-3 font-medium" style="color: #2b4d2b;">
                            "{{ $pengumumanTerbaru->deskripsi }}"
                        </p>

                        <p class="mt-3 pt-3 text-[11px] font-bold flex items-center gap-1 transition-all duration-200"
                            style="border-top: 1px solid #b5d5b5; color: #386138;">
                            <span>Baca selengkapnya</span>
                            <span class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                        </p>
                    </div>
                </a>
            @endif
        </div>
    </div>

    @if ($tagihanDitolakLain->count() > 0)
        <div class="mt-4 space-y-3 animate-in" style="animation-delay: 0.05s">
            @foreach ($tagihanDitolakLain as $t)
                @php
                    $bulanLabelDitolak = \Carbon\Carbon::createFromFormat('Y-m', $t->bulan_tagihan)->translatedFormat(
                        'F Y',
                    );

                    $alasanLain = $t->pembayaran->sortByDesc('created_at')->firstWhere('status_verifikasi', 'failed')
                        ?->alasan_penolakan;
                @endphp
                <div
                    class="bg-terracotta-50 border border-terracotta-200 rounded-2xl shadow-sm p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                    <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                        <div
                            class="w-10 h-10 rounded-2xl bg-terracotta-100 text-terracotta-600 ring-4 ring-terracotta-100/50 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                            @include('partial.icons.warning', ['class' => 'w-5 h-5'])
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-terracotta-800 truncate">
                                Bukti Pembayaran Ditolak &mdash; {{ $bulanLabelDitolak }}
                            </p>
                            <p class="text-xs text-terracotta-700 mt-0.5 leading-relaxed">
                                <span class="font-semibold">Alasan:</span> {{ $alasanLain ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('penghuni.tagihan.index') }}"
                        class="w-full sm:w-auto text-center shrink-0 text-xs font-bold text-terracotta-700 bg-white border border-terracotta-300 rounded-xl px-4 py-2.5 hover:bg-terracotta-100 transition-colors duration-150 shadow-2xs">
                        Upload Ulang
                    </a>

                </div>
            @endforeach
        </div>
    @endif
@endsection
