@if ($paginator->hasPages())
    <div class="flex w-full items-center justify-between gap-4 border-t border-gray-200 bg-white px-6 py-4">
        <span class="text-sm text-gray-600">Page {{ $paginator->currentPage() }}</span>
        <div role="navigation" aria-label="Reservation pagination" class="reservation-pagination-nav ml-auto flex items-center gap-2 bg-transparent shadow-none">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-400">
                    « Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                    « Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                    Next »
                </a>
            @else
                <span aria-disabled="true" class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-400">
                    Next »
                </span>
            @endif
        </div>
    </div>
@endif
