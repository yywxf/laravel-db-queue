<style>
    .pagination > li > a, .pagination > li > span, .pagination > li > select {
        border-radius: 4px;
        margin: 5px;
    }
</style>
@if ($paginator->hasPages())
    <ul class="pagination justify-content-center" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="page-link" aria-hidden="true">上一页</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" data-page="{{ $paginator->currentPage()-1 }}" rel="prev" aria-label="@lang('pagination.previous')">上一页</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" data-page="{{ $page }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" data-page="{{ $paginator->currentPage()+1 }}" rel="next" aria-label="@lang('pagination.next')">下一页</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="page-link" aria-hidden="true">下一页</span>
            </li>
        @endif
        <li class="page-item">
            <select id="selectPerPage" class="form-control">
                @foreach ($perPages as $perPage)
                    @if ($perPage == $paginator->perPage())
                        <option value="{{ $perPage }}" selected>{{ $perPage }}/页</option>
                    @else
                        <option value="{{ $perPage }}">{{ $perPage }}/页</option>
                    @endif
                @endforeach
            </select>
        </li>
    </ul>
@endif
