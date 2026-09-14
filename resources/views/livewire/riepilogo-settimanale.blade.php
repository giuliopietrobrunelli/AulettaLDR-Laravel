<div id="riepilogo-settimanale-wrapper">
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
                <div class="booking-week-row @if($settimana['passata']) past @endif @if($settimana['corrente']) current-week @endif">
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