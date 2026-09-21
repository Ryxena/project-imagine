@extends('layout.penghuni.PenghuniDashboardLayout')

@section('page-title', 'Papan Pengumuman')
@section('page-subtitle', 'Informasi terbaru dari pengelola kost.')

@section('content')
    @php
        $items = $pengumumans->getCollection();$hero = $pengumumans->currentPage() === 1 ?$items->first() : null;
        $rest =$hero ? $items->slice(1) :$items;

        $typeMeta = fn($type) => match ($type) {
            'penting' => ['bg-terracotta-600 text-white', 'bg-terracotta-100 text-terracotta-600', 'Penting'],
            'informasi' => ['bg-sage-700 text-white', 'bg-sage-100 text-sage-700', 'Informasi'],
            default => ['bg-cream-600 text-white', 'bg-cream-100 text-cream-600', 'Umum'],
        };
    @endphp

    @if ($hero)
        @php 
            [$heroBadge, ,$heroLabel] = $typeMeta($hero->type); 
            $heroAdminImage =$hero->admin->image ?? null;
            $heroAdminName =$hero->admin->name ?? 'Pengelola';
        @endphp
        <div class="bg-white rounded-2xl border border-cream-200 shadow-sm overflow-hidden animate-in">
            <div class="h-1.5 {{ str($heroBadge)->before(' ') }}"></div>
            <div class="p-6">
                <div class="flex items-center gap-2.5">
                    <span
                        class="{{ $heroBadge }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $heroLabel }}</span>
                    <p class="text-xs text-cream-500">
                        {{ \Carbon\Carbon::parse($hero->tanggal_publish)->translatedFormat('d F Y') }} &bull;
                        {{ $hero->created_at->translatedFormat('H.i') }} WIB
                    </p>
                </div>
                <p class="mt-3 text-xl font-bold text-cream-900">{{ $hero->judul }}</p>
                <p class="mt-2 text-sm text-cream-700 leading-relaxed">{{ $hero->deskripsi }}</p>
  
                <div class="mt-4 pt-4 border-t border-cream-100 flex items-center gap-2.5">
                    @if ($heroAdminImage)
                        <img src="{{ asset('storage/' . $heroAdminImage) }}" alt="Foto {{ $heroAdminName }}"
                            class="w-7 h-7 rounded-full object-cover border border-sage-200 shrink-0 shadow-sm">
                    @else
                        <div
                            class="w-7 h-7 rounded-full bg-sage-200 text-sage-800 text-[11px] font-semibold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($heroAdminName, 0, 1)) }}
                        </div>
                    @endif
                    <p class="text-xs font-medium text-cream-700">{{ $heroAdminName }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($rest->count() > 0)
        <div class="mt-6 relative animate-in" style="animation-delay: 0.05s">
            <div class="absolute left-[15px] top-2 bottom-2 w-px bg-cream-200"></div>

            <div class="space-y-5">
                @foreach ($rest as $p)
                    @php 
                        [, $tint,$label] = $typeMeta($p->type); 
                        $adminImage =$p->admin->image ?? null;
                        $adminName =$p->admin->name ?? 'Pengelola';
                    @endphp
                    <div class="relative pl-10">
                        <div
                            class="absolute left-0 top-1 w-8 h-8 rounded-full {{ $tint }} border-4 border-cream-page flex items-center justify-center">
                            @include('partial.icons.megaphone', ['class' => 'w-3.5 h-3.5'])
                        </div>

                        <div class="bg-white rounded-xl border border-cream-200 shadow-sm p-4">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="{{ $tint }} text-[10px] font-semibold px-2.5 py-1 rounded-full">{{ $label }}</span>
                                <p class="text-[11px] text-cream-500">
                                    {{ \Carbon\Carbon::parse($p->tanggal_publish)->translatedFormat('d M Y') }}</p>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-cream-900">{{ $p->judul }}</p>

                            <p class="pengumuman-desc mt-1 text-xs text-cream-600 leading-relaxed line-clamp-2"
                                data-id="{{ $p->id }}">
                                {{ $p->deskripsi }}
                            </p>
                            <button onclick="toggleExpand({{ $p->id }})" id="toggle-btn-{{ $p->id }}"
                                class="hidden mt-1 text-[11px] font-semibold text-sage-700 hover:text-sage-800 transition-colors duration-150">
                                Lihat selengkapnya
                            </button>

                            <p class="mt-2 text-[11px] text-cream-500">— {{ $adminName }}, diposting
                                {{ $p->created_at->translatedFormat('d M, H.i') }} WIB</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (!$hero &&$rest->count() === 0)
        <div class="bg-white rounded-xl border border-cream-200 px-4 py-8 text-center shadow-sm animate-in">
            <p class="text-sm text-cream-600">Belum ada pengumuman.</p>
        </div>
    @endif

    @if ($pengumumans->hasPages())
        <div class="mt-6">
            {{ $pengumumans->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.pengumuman-desc').forEach((el) => {
                if (el.scrollHeight > el.clientHeight) {
                    const btn = document.getElementById(`toggle-btn-${el.dataset.id}`);
                    if (btn) btn.classList.remove('hidden');
                }
            });
        });

        function toggleExpand(id) {
            const desc = document.querySelector(`.pengumuman-desc[data-id="${id}"]`);
            const btn = document.getElementById(`toggle-btn-${id}`);
            const isExpanded = !desc.classList.contains('line-clamp-2');

            if (isExpanded) {
                desc.classList.add('line-clamp-2');
                btn.textContent = 'Lihat selengkapnya';
            } else {
                desc.classList.remove('line-clamp-2');
                btn.textContent = 'Sembunyikan';
            }
        }
    </script>
@endpush