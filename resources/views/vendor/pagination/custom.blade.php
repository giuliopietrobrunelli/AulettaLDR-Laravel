@if ($paginator->hasPages())
    <div class="horizontal-container" style="justify-content: center; gap: 8px; margin-top: 16px;">
        @if ($paginator->onFirstPage())
            <button type="button" class="mini" disabled>
                <i data-lucide="chevron-left"></i>
            </button>
        @else
            <button type="button" class="mini" wire:click="previousPage" wire:loading.attr="disabled">
                <i data-lucide="chevron-left"></i>
            </button>
        @endif

        <span style="font-size:12px;opacity:0.6;padding:0 8px;">
            Pagina {{ $paginator->currentPage() }} di {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <button type="button" class="mini" wire:click="nextPage" wire:loading.attr="disabled">
                <i data-lucide="chevron-right"></i>
            </button>
        @else
            <button type="button" class="mini" disabled>
                <i data-lucide="chevron-right"></i>
            </button>
        @endif
    </div>
@endif