@if ($paginator->hasPages())
  <nav aria-label="@lang("pagination.label")">
    <ul class="pagination">
      {{-- Previous Page Link --}}
      @if ($paginator->onFirstPage())
        <li class="disabled mobile" aria-disabled="true" aria-label="@lang("pagination.previous")">
          <span aria-hidden="true">
            <svg width="10" height="12" viewBox="0 0 10 12" xmlns="http://www.w3.org/2000/svg">
              <line x1="1" y1="1" x2="1" y2="11" stroke="currentColor" stroke-width="1.2" />
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="9 1 4 6 9 11" />
            </svg>
          </span>
        </li>
        <li class="disabled mobile" aria-disabled="true" aria-label="@lang("pagination.previous")">
          <span aria-hidden="true">
            <svg width="7" height="12" viewBox="0 0 7 12" xmlns="http://www.w3.org/2000/svg">
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="6 1 1 6 6 11" />
            </svg>
          </span>
        </li>
      @else
        <li class="mobile">
          <a href="{{ $paginator->url(1) }}" aria-label="@lang("pagination.first")">
            <svg width="10" height="12" viewBox="0 0 10 12" xmlns="http://www.w3.org/2000/svg">
              <line x1="1" y1="1" x2="1" y2="11" stroke="currentColor" stroke-width="1.2" />
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="9 1 4 6 9 11" />
            </svg>
          </a>
        </li>
        <li class="mobile">
          <a href="{{ $paginator->previousPageUrl() }}" aria-label="@lang("pagination.previous")">
            <svg width="7" height="12" viewBox="0 0 7 12" xmlns="http://www.w3.org/2000/svg">
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="6 1 1 6 6 11" />
            </svg>
          </a>
        </li>
      @endif

      {{-- Pagination Elements --}}
      @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
          <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <li class="active" aria-current="page"><span>{{ $page }}</span></li>
              <li class="active mobile mobile-only" aria-current="page">
                <span>@lang("pagination.page", ["page" => $page, "total" => $paginator->lastPage()])</span>
              </li>
            @else
              <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <li class="mobile">
          <a href="{{ $paginator->nextPageUrl() }}" aria-label="@lang("pagination.next")">
            <svg width="7" height="12" viewBox="0 0 7 12" xmlns="http://www.w3.org/2000/svg">
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="1 1 6 6 1 11" />
            </svg>
          </a>
        </li>
        <li class="mobile">
          <a href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="@lang("pagination.last")">
            <svg width="10" height="12" viewBox="0 0 10 12" xmlns="http://www.w3.org/2000/svg">
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="1 1 6 6 1 11" />
              <line x1="9" y1="1" x2="9" y2="11" stroke="currentColor" stroke-width="1.2" />
            </svg>
          </a>
        </li>
      @else
        <li class="disabled mobile" aria-disabled="true" aria-label="@lang("pagination.next")">
          <span aria-hidden="true">
            <svg width="7" height="12" viewBox="0 0 7 12" xmlns="http://www.w3.org/2000/svg">
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="1 1 6 6 1 11" />
            </svg>
          </span>
        </li>
        <li class="disabled mobile" aria-disabled="true" aria-label="@lang("pagination.last")">
          <span aria-hidden="true">
            <svg width="10" height="12" viewBox="0 0 10 12" xmlns="http://www.w3.org/2000/svg">
              <polyline fill="none" stroke="currentColor" stroke-width="1.2" points="1 1 6 6 1 11" />
              <line x1="9" y1="1" x2="9" y2="11" stroke="currentColor" stroke-width="1.2" />
            </svg>
          </span>
        </li>
      @endif
    </ul>
  </nav>
@endif
