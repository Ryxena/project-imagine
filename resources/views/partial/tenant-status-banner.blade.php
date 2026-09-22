@if (($tenantStatus ?? 'active') !== 'active')
    <div
        class="mb-5 rounded-xl border px-4 py-3 flex items-start gap-3 animate-in
        {{ $tenantStatus === 'checked_out' ? 'bg-cream-100 border-cream-300' : 'bg-amber-50 border-amber-200' }}">
        <div
            class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
            {{ $tenantStatus === 'checked_out' ? 'bg-cream-200 text-cream-600' : 'bg-amber-100 text-amber-600' }}">
            @include('partial.icons.warning', ['class' => 'w-4 h-4'])
        </div>
        <div>
            @if ($tenantStatus === 'checked_out')
                <p class="text-sm font-semibold text-cream-800">Anda sudah tidak lagi menjadi penghuni aktif</p>
                <p class="text-xs text-cream-600 mt-0.5">
                    Masa tinggal Anda berakhir pada
                    {{ \Carbon\Carbon::parse($latestPenghunian->tanggal_checkout)->translatedFormat('d F Y') }}.
                </p>
            @elseif ($tenantStatus === 'unassigned')
                <p class="text-sm font-semibold text-amber-700">Anda belum ditempatkan ke kamar manapun</p>
                <p class="text-xs text-amber-700/80 mt-0.5">
                    Hubungi pengelola kost untuk penempatan kamar.
                </p>
            @else
                <p class="text-sm font-semibold text-cream-800">Belum ada data penempatan kamar</p>
                <p class="text-xs text-cream-600 mt-0.5">Hubungi pengelola kost untuk informasi lebih lanjut.</p>
            @endif
        </div>
    </div>
@endif
