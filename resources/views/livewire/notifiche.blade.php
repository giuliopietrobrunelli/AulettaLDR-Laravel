<div id="notifiche-wrapper">
    <button data-modal="notifiche" class="notification-button" title="Le tue notifiche">
        <i data-lucide="inbox"></i>
        <span class="notification-dot @if($richieste->isEmpty()) hidden @endif"></span>
    </button>

    <div id="modal-notifiche" class="modal">
        <div class="modal-header">
            <span class="modal-title">Notifiche</span>
        </div>
        <div class="lista-notifiche">
            @forelse ($richieste as $richiesta)
                <div class="notifica-row" wire:key="richiesta-{{ $richiesta->id_richiesta }}">
                    <span class="booking-week-label">
                        <strong>{{ $richiesta->mittente->nome }} {{ $richiesta->mittente->cognome }}</strong>
                        ti propone il turno del
                        {{ \Illuminate\Support\Carbon::parse($richiesta->prenotazione->data_prenotazione)->locale('it')->translatedFormat('j F') }}
                        ({{ substr($richiesta->prenotazione->turno->orario_inizio, 0, 5) }} - {{ substr($richiesta->prenotazione->turno->orario_fine, 0, 5) }})
                    </span>
                    <div class="horizontal-container">
                        <button type="button" class="w-text action-btn" wire:click="accetta({{ $richiesta->id_richiesta }})">
                            <i data-lucide="check"></i>
                        </button>
                        <button type="button" class="w-text danger" wire:click="rifiuta({{ $richiesta->id_richiesta }})">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </div>
            @empty
                <span class="modal-advise take-action">Nessuna notifica al momento.</span>
            @endforelse
        </div>
    </div>

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>