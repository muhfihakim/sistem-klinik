@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-outline-primary">

            {{-- First Page Link --}}
            <li class="page-item first {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true">
                        <i class="icon-base ri ri-skip-back-mini-line icon-22px"></i>
                    </span>
                @else
                    <button type="button" class="page-link" wire:click="gotoPage(1)" aria-label="First">
                        <i class="icon-base ri ri-skip-back-mini-line icon-22px"></i>
                    </button>
                @endif
            </li>

            {{-- Previous Page Link --}}
            <li class="page-item prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true">
                        <i class="icon-base ri ri-arrow-left-s-line icon-22px"></i>
                    </span>
                @else
                    <button type="button" class="page-link" wire:click="previousPage" aria-label="Previous">
                        <i class="icon-base ri ri-arrow-left-s-line icon-22px"></i>
                    </button>
                @endif
            </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">
                                    {{ $page }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            <li class="page-item next {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if ($paginator->hasMorePages())
                    <button type="button" class="page-link" wire:click="nextPage" aria-label="Next">
                        <i class="icon-base ri ri-arrow-right-s-line icon-22px"></i>
                    </button>
                @else
                    <span class="page-link" aria-hidden="true">
                        <i class="icon-base ri ri-arrow-right-s-line icon-22px"></i>
                    </span>
                @endif
            </li>

            {{-- Last Page Link --}}
            <li class="page-item last {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if ($paginator->hasMorePages())
                    <button type="button" class="page-link" wire:click="gotoPage({{ $paginator->lastPage() }})"
                        aria-label="Last">
                        <i class="icon-base ri ri-skip-forward-mini-line icon-22px"></i>
                    </button>
                @else
                    <span class="page-link" aria-hidden="true">
                        <i class="icon-base ri ri-skip-forward-mini-line icon-22px"></i>
                    </span>
                @endif
            </li>

        </ul>
    </nav>
@endif
