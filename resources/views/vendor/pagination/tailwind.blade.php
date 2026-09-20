@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center pt-4 px-1">

        <div class="flex items-center gap-1.5">

            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center justify-center w-8 h-8 text-cream-300 bg-cream-50/60 border border-cream-200/60 rounded-xl cursor-not-allowed shadow-none"
                    aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center justify-center w-8 h-8 text-cream-700 bg-white border border-cream-200 rounded-xl hover:bg-sage-50 hover:border-sage-300 hover:text-sage-700 transition-all duration-150 shadow-2xs"
                    aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-cream-400 cursor-default">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-sage-700 border border-sage-700 rounded-xl shadow-xs cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-cream-700 bg-white border border-cream-200 rounded-xl hover:bg-sage-50 hover:border-sage-300 hover:text-sage-700 transition-all duration-150 shadow-2xs">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center justify-center w-8 h-8 text-cream-700 bg-white border border-cream-200 rounded-xl hover:bg-sage-50 hover:border-sage-300 hover:text-sage-700 transition-all duration-150 shadow-2xs"
                    aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span
                    class="inline-flex items-center justify-center w-8 h-8 text-cream-300 bg-cream-50/60 border border-cream-200/60 rounded-xl cursor-not-allowed shadow-none"
                    aria-hidden="true">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif

        </div>

    </nav>
@endif
