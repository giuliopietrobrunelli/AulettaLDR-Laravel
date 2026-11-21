<div id="riepilogo-settimanale-wrapper">
    @if ($mobile)
        <button type="button" class="w-text" wire:click="$set('apertoMobile', true)" title="Limiti delle tue prenotazioni">
            <i data-lucide="calendar-clock"></i>
            <span>{{ $conteggioAttuale }}/{{ $limiteSettimanale }}</span>
        </button>
    @else
        <button class="w-text" data-modal="le-mie-prenotazioni" title="Limiti delle tue prenotazioni">
            <i data-lucide="calendar-clock"></i>
            <span>{{ $conteggioAttuale }}/{{ $limiteSettimanale }}</span>
        </button>

        <div id="modal-le-mie-prenotazioni" class="modal">
            <div class="modal-header auto-gap">
                <span class="modal-title">Le mie prenotazioni</span>
                <span class="modal-subtitle">{{ $conteggioAttuale }}/{{ $limiteSettimanale }}</span>
            </div>
            <div class="lista-notifiche">
                @foreach ($settimane as $settimana)
                    <div
                        class="booking-week-row @if($settimana['passata']) past @endif @if($settimana['corrente']) current-week @endif">
                        <span class="booking-week-label">
                            {{ $settimana['inizio']->locale('it')->translatedFormat('j M') }}
                            –
                            {{ $settimana['fine']->locale('it')->translatedFormat('j M') }}
                        </span>
                        <span class="booking-week-count @if($settimana['conteggio'] >= $limiteSettimanale) full @endif">
                            {{ $settimana['conteggio'] }}/{{ $limiteSettimanale }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($mobile && $apertoMobile)
        @teleport('body')
        <div class="full-modal showing" aria-hidden="false" wire:click.self="chiudiRiepilogo">
            <div class="modal showing" style="position: relative; top: auto; right: auto; width: 100%; max-width: 420px;">
                <div class="modal-header auto-gap" style="align-items: center;">
                    <span class="modal-title" style="color: var(--white);">Le mie prenotazioni</span>
                    <div class="horizontal-container" style="gap: 12px; align-items: center;">
                        <span class="modal-subtitle"
                            style="color: var(--white);">{{ $conteggioAttuale }}/{{ $limiteSettimanale }}</span>
                        <button type="button" class="no-style" wire:click="chiudiRiepilogo" aria-label="Chiudi"
                            style="color: var(--white); padding: 0; line-height: 0;">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                </div>
                <div class="lista-notifiche">
                    @foreach ($settimane as $settimana)
                        <div
                            class="booking-week-row @if($settimana['passata']) past @endif @if($settimana['corrente']) current-week @endif">
                            <span class="booking-week-label">
                                {{ $settimana['inizio']->locale('it')->translatedFormat('j M') }}
                                –
                                {{ $settimana['fine']->locale('it')->translatedFormat('j M') }}
                            </span>
                            <span class="booking-week-count @if($settimana['conteggio'] >= $limiteSettimanale) full @endif">
                                {{ $settimana['conteggio'] }}/{{ $limiteSettimanale }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>