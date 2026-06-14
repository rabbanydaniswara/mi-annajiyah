@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman kegiatan" class="space-y-4">
        <p class="text-center text-sm text-gray-500">
            Halaman <strong class="text-[var(--color-primary)]">{{ $paginator->currentPage() }}</strong>
            dari <strong class="text-[var(--color-primary)]">{{ $paginator->lastPage() }}</strong>
        </p>

        <div class="flex flex-wrap items-center justify-center gap-2">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true"
                      aria-label="Halaman sebelumnya tidak tersedia"
                      class="inline-flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-gray-100 px-3 text-sm font-semibold text-gray-400">
                    <span aria-hidden="true">&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   rel="prev"
                   aria-label="Ke halaman sebelumnya"
                   class="inline-flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 text-sm font-semibold text-gray-600 shadow-sm transition hover:border-[var(--color-primary)] hover:bg-green-50 hover:text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-primary)]">
                    <span aria-hidden="true">&larr;</span>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-hidden="true" class="inline-flex h-11 min-w-11 items-center justify-center text-gray-400">
                        &hellip;
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span aria-current="page"
                                  aria-label="Halaman {{ $page }}, halaman saat ini"
                                  class="inline-flex h-11 min-w-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-3 text-sm font-bold text-white shadow-md">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               aria-label="Ke halaman {{ $page }}"
                               class="inline-flex h-11 min-w-11 items-center justify-center rounded-xl border border-gray-200 bg-white px-3 text-sm font-semibold text-gray-600 shadow-sm transition hover:border-[var(--color-primary)] hover:bg-green-50 hover:text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-primary)]">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   rel="next"
                   aria-label="Ke halaman berikutnya"
                   class="inline-flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 text-sm font-semibold text-gray-600 shadow-sm transition hover:border-[var(--color-primary)] hover:bg-green-50 hover:text-[var(--color-primary)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-primary)]">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @else
                <span aria-disabled="true"
                      aria-label="Halaman berikutnya tidak tersedia"
                      class="inline-flex h-11 items-center gap-2 rounded-xl border border-gray-200 bg-gray-100 px-3 text-sm font-semibold text-gray-400">
                    <span class="hidden sm:inline">Berikutnya</span>
                    <span aria-hidden="true">&rarr;</span>
                </span>
            @endif
        </div>
    </nav>
@endif
