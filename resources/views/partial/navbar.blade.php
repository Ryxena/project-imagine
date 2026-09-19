<header class="flex justify-end items-center px-8 py-5">
    <a href="{{ route('admin.notifikasi.index') }}" class="relative text-cream-600 hover:text-cream-900">
        <header class="flex justify-end items-center px-8 py-5">
            <a href="{{ route('admin.notifikasi.index') }}" class="relative text-cream-600 hover:text-cream-900">
                @include('partial.icons.bell', ['class' => 'w-6 h-6'])
            </a>
        </header>
        {{-- badge unread (butuh data count dari controller, belum ada sekarang) --}}
    </a>
</header>