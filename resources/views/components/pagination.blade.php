@if($paginator->hasPages())
<nav class="flex items-center justify-between">
    <!-- Mobile View -->
    <div class="flex flex-1 justify-between sm:hidden">
        @if($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-slate-700 border border-slate-600 cursor-default leading-5 rounded-lg">
                {{ __('pagination.previous') }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-700 border border-slate-600 leading-5 rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                {{ __('pagination.previous') }}
            </a>
        @endif

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-white bg-slate-700 border border-slate-600 leading-5 rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                {{ __('pagination.next') }}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-slate-700 border border-slate-600 cursor-default leading-5 rounded-lg">
                {{ __('pagination.next') }}
            </span>
        @endif
    </div>

    <!-- Desktop View -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <!-- Results Info -->
        <div>
            <p class="text-sm text-gray-400 leading-5">
                {{ __('pagination.showing') }}
                @if($paginator->firstItem())
                    <span class="font-medium text-white">{{ $paginator->firstItem() }}</span>
                    {{ __('pagination.to') }}
                    <span class="font-medium text-white">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {{ __('pagination.of') }}
                <span class="font-medium text-white">{{ $paginator->total() }}</span>
                {{ __('pagination.results') }}
            </p>
        </div>

        <!-- Pagination Links -->
        <div>
            <span class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px">
                {{-- Previous Page Link --}}
                @if($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-slate-700 border border-slate-600 cursor-default rounded-l-lg leading-5" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 rounded-l-lg leading-5 hover:bg-slate-600 hover:text-white focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach($elements ?? [] as $element)
                    {{-- "Three Dots" Separator --}}
                    @if(is_string($element))
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-slate-700 border border-slate-600 cursor-default leading-5">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 leading-5 hover:bg-slate-600 hover:text-white focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" aria-label="{{ __('pagination.goto_page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-slate-700 border border-slate-600 rounded-r-lg leading-5 hover:bg-slate-600 hover:text-white focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-slate-700 border border-slate-600 cursor-default rounded-r-lg leading-5" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </div>
</nav>
@endif 