<div id="notifiche-wrapper">
    @if ($mobile)
        <button type="button" class="notification-button" wire:click="$set('apertoMobile', true)" title="Le tue notifiche">
            <i data-lucide="inbox"></i>
            <span class="notification-dot @if($richieste->isEmpty()) hidden @endif"></span>
        </button>
    @else
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
                            ({{ substr($richiesta->prenotazione->turno->orario_inizio, 0, 5) }} -
                            {{ substr($richiesta->prenotazione->turno->orario_fine, 0, 5) }})
                        </span>
                        <div class="horizontal-container">
                            <button type="button" class="w-text action-btn"
                                wire:click="accetta({{ $richiesta->id_richiesta }})">
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
    @endif

    @if ($mobile && $apertoMobile)
        @teleport('body')
        <div class="full-modal showing" aria-hidden="false" wire:click.self="chiudiNotifiche">
            <div class="modal showing" style="position: relative; top: auto; right: auto; width: 100%; max-width: 420px;">
                <div class="modal-header auto-gap">
                    <span class="modal-title" style="color: var(--white);">Notifiche</span>
                    <button type="button" class="no-style" wire:click="chiudiNotifiche" aria-label="Chiudi"
                        style="color: var(--white);">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <div class="lista-notifiche">
                    @forelse ($richieste as $richiesta)
                        <div class="notifica-row" wire:key="richiesta-mobile-{{ $richiesta->id_richiesta }}">
                            <span class="booking-week-label">
                                <strong>{{ $richiesta->mittente->nome }} {{ $richiesta->mittente->cognome }}</strong>
                                ti propone il turno del
                                {{ \Illuminate\Support\Carbon::parse($richiesta->prenotazione->data_prenotazione)->locale('it')->translatedFormat('j F') }}
                                ({{ substr($richiesta->prenotazione->turno->orario_inizio, 0, 5) }} -
                                {{ substr($richiesta->prenotazione->turno->orario_fine, 0, 5) }})
                            </span>
                            <div class="horizontal-container">
                                <button type="button" class="w-text action-btn"
                                    wire:click="accetta({{ $richiesta->id_richiesta }})">
                                    <i data-lucide="check"></i>
                                </button>
                                <button type="button" class="w-text danger"
                                    wire:click="rifiuta({{ $richiesta->id_richiesta }})">
                                    <i data-lucide="x"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <span class="modal-advise take-action">Nessuna notifica al momento.</span>
                    @endforelse
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>