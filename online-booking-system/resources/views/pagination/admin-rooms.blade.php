{{--
    Custom pagination view for the Admin Rooms page only.
    It renders Laravel's built-in paginate(5) links as:
        Page X                         « Previous  Next »

    It is passed to $rooms->links('pagination.admin-rooms') from admin/rooms.blade.php.
    - $paginator: the LengthAwarePaginator created by Room::orderBy('room_number')->paginate(5).
    - $elements:  array of page links (arrays of page => url) with "..." string separators.
    - The whole block is skipped automatically by @if($paginator->hasPages()),
      so no pagination shows when there are 5 or fewer rooms (single page).
    - Previous is disabled on the first page; Next is disabled on the last page.
--}}
@if ($paginator->hasPages())
    <div class="flex w-full items-center justify-between gap-4 border-t border-gray-200 bg-white px-6 py-4">
        <span class="text-sm text-gray-600">Page {{ $paginator->currentPage() }}</span>
        <nav role="navigation" aria-label="Room pagination" class="room-pagination ml-auto flex items-center gap-2 bg-transparent shadow-none">
            {{-- Previous link (rendered as a disabled span on the first page) --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-400">
                    « Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                    « Previous
                </a>
            @endif

            {{-- Next link (rendered as a disabled span on the last page) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                    Next »
                </a>
            @else
                <span aria-disabled="true" class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-400">
                    Next »
                </span>
            @endif
        </nav>
    </div>
@endif
