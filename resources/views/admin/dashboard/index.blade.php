@extends('layout.admin.AdminDashboardLayout')

@php
    $hour = now('Asia/Jakarta')->hour;
    $greeting = match (true) {
        $hour < 11 => 'Selamat Pagi',
        $hour < 15 => 'Selamat Siang',
        $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };
@endphp

@section('page-title', $greeting . ', ' . auth()->user()->name . '!')
@section('page-subtitle', 'Ada beberapa hal yang perlu ditinjau hari ini.')

@section('content')
    <div class="flex flex-wrap gap-2.5 animate-in my-1">
        <button
            class="flex items-center gap-2 bg-sage-700 text-white text-xs font-medium px-4 py-2 rounded-full hover:bg-sage-800 hover:shadow-md active:scale-[0.98] transition-all duration-150 ease-out">
            @include('partial.icons.users', ['class' => 'w-3.5 h-3.5'])
            Tambah Penghuni
        </button>
        <button
            class="flex items-center gap-2 bg-white border border-cream-300 text-cream-900 text-xs font-medium px-4 py-2 rounded-full hover:border-sage-400 hover:shadow-sm active:scale-[0.98] transition-all duration-150 ease-out">
            @include('partial.icons.receipt', ['class' => 'w-3.5 h-3.5'])
            Generate Tagihan
        </button>
        <button
            class="flex items-center gap-2 bg-white border border-cream-300 text-cream-900 text-xs font-medium px-4 py-2 rounded-full hover:border-sage-400 hover:shadow-sm active:scale-[0.98] transition-all duration-150 ease-out">
            @include('partial.icons.megaphone', ['class' => 'w-3.5 h-3.5'])
            Buat Pengumuman
        </button>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-5">
        <a href="{{ route('admin.kamar.index') }}"
            class="animate-in block bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out"
            style="animation-delay: 0.05s">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-cream-600">Kamar Terisi</p>
                <div class="w-8 h-8 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center">
                    @include('partial.icons.door', ['class' => 'w-3.5 h-3.5'])
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-cream-900">
                {{ $kpi['kamar_terisi'] }} <span class="text-sm font-normal text-cream-600">/
                    {{ $kpi['kamar_total'] }}</span>
            </p>
            <div class="mt-2.5 h-1.5 bg-cream-100 rounded-full overflow-hidden">
                <div class="h-full bg-sage-600 rounded-full transition-all duration-700 ease-out"
                    style="width: {{ ($kpi['kamar_terisi'] / max($kpi['kamar_total'], 1)) * 100 }}%"></div>
            </div>
        </a>

        <a href="{{ route('admin.penghuni.index') }}"
            class="animate-in block bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out"
            style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-cream-600">Penghuni Aktif</p>
                <div class="w-8 h-8 rounded-full bg-terracotta-100 text-terracotta-600 flex items-center justify-center">
                    @include('partial.icons.users', ['class' => 'w-3.5 h-3.5'])
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-cream-900">{{ $kpi['penghuni_aktif'] }}</p>
            @if ($kpi['penghuni_bulan_ini'] > 0)
                <p class="mt-1 text-[11px] font-medium text-terracotta-600">↑ +{{ $kpi['penghuni_bulan_ini'] }} bulan ini
                </p>
            @endif
        </a>

        <a href="{{ route('admin.tagihan.index') }}"
            class="animate-in block bg-white rounded-xl p-4 shadow-sm border border-cream-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 ease-out"
            style="animation-delay: 0.15s">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-cream-600">Tagihan Lunas Bulan Ini</p>
                <div class="w-8 h-8 rounded-full bg-cream-200 text-cream-600 flex items-center justify-center">
                    @include('partial.icons.receipt', ['class' => 'w-3.5 h-3.5'])
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-cream-900">
                {{ $kpi['tagihan_lunas'] }} <span class="text-sm font-normal text-cream-600">/
                    {{ $kpi['tagihan_total'] }}</span>
            </p>
            <p class="mt-1 text-[11px] text-cream-600">Menunggu {{ $kpi['tagihan_total'] - $kpi['tagihan_lunas'] }}
                pembayaran
            </p>
        </a>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="animate-in" style="animation-delay: 0.2s">
            <h2 class="flex items-center gap-1.5 text-base font-semibold text-cream-900 mb-2.5">
                <span class="text-terracotta-600">@include('partial.icons.warning', ['class' => 'w-3.5 h-3.5'])</span>
                Perlu ditindaklanjuti
            </h2>

            @if (count($perluTindakan) > 0)
                <div class="bg-white rounded-xl border border-cream-200 divide-y divide-cream-200 shadow-sm">
                    @foreach ($perluTindakan as $item)
                        @php
                            [$tint, $icon] = match ($item['type']) {
                                'verifikasi' => ['bg-cream-200 text-cream-600', 'receipt'],
                                'telat' => ['bg-terracotta-100 text-terracotta-600', 'warning'],
                                'keluhan' => ['bg-amber-100 text-amber-600', 'megaphone'],
                                'kamar_kosong' => ['bg-cream-200 text-cream-600', 'door'],
                                default => ['bg-cream-100 text-cream-600', 'warning'],
                            };
                        @endphp
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 px-4 py-2.5 hover:bg-cream-50 transition-colors duration-150 ease-out">
                            <div
                                class="shrink-0 w-8 h-8 rounded-full {{ $tint }} flex items-center justify-center">
                                @include('partial.icons.' . $icon, ['class' => 'w-3.5 h-3.5'])
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-cream-900">{{ $item['label'] }}</p>
                                <p class="text-[11px] text-cream-600 truncate">{{ $item['meta'] }}</p>
                            </div>
                            @if (isset($item['count']))
                                <span
                                    class="shrink-0 bg-sage-700 text-white text-[10px] font-semibold rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ $item['count'] }}
                                </span>
                            @else
                                <span class="shrink-0 text-cream-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-cream-200 px-4 py-6 text-center shadow-sm">
                    <p class="text-xs text-cream-600">Semua beres! Nggak ada yang perlu ditindaklanjuti.</p>
                </div>
            @endif
        </div>

        <div class="animate-in" style="animation-delay: 0.25s">
            <h2 class="flex items-center gap-1.5 text-base font-semibold text-cream-900 mb-2.5">
                <span class="text-cream-500">@include('partial.icons.clock', ['class' => 'w-3.5 h-3.5'])</span>
                Aktivitas Terbaru
            </h2>

            @if (count($aktivitas) > 0)
                <div class="bg-white rounded-xl border border-cream-200 p-3.5 shadow-sm">
                    <div class="space-y-4">
                        @foreach ($aktivitas as $item)
                            <div class="flex gap-2.5">
                                <div
                                    class="shrink-0 w-7 h-7 rounded-full bg-cream-100 text-cream-600 flex items-center justify-center">
                                    @include('partial.icons.clock', ['class' => 'w-3 h-3'])
                                </div>
                                <div class="pt-0.5">
                                    <p class="text-[13px] text-cream-900 leading-tight">{!! $item['text'] !!}</p>
                                    <p class="text-[11px] text-cream-600 mt-0.5">{{ $item['waktu'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl border border-cream-200 px-4 py-6 text-center shadow-sm">
                    <p class="text-xs text-cream-600">Belum ada aktivitas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
