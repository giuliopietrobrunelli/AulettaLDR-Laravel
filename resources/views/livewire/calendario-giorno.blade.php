<div id="calendario-giorno-wrapper">
    <div id="calendar-header">
        <div id="indicatore-data-prenota-turni-container" class="horizontal-container">
            <h1 id="current-date">{{ $titoloGiorno }}</h1>
        </div>

        <div id="day-view-actions" class="horizontal-container" data-view="day">
            <button type="button" wire:click="giornoPrecedente" class="mini" aria-label="Vai al giorno precedente">
                <i data-lucide="chevron-left"></i>
            </button>
            <button type="button" wire:click="giornoOggi" class="mini" aria-label="Vai al giorno attuale">
                <span>Oggi</span>
            </button>
            <button type="button" wire:click="giornoSuccessivo" class="mini" aria-label="Vai al giorno successivo">
                <i data-lucide="chevron-right"></i>
            </button>

            <div class="vertical-separator"></div>

            <button type="button" class="w-text" data-modal="prenota">
                <i data-lucide="plus"></i>
                <span>Prenota</span>
            </button>
        </div>
    </div>

    <div id="day-schedules" class="day-calendar">
        @foreach ($righe as $i => $riga)
            <div
                wire:key="turno-{{ $riga['turno']->id_turno }}"
                class="day-turn"
                data-status="{{ $riga['stato'] }}"
                @if ($riga['stato'] === 'available')
                    wire:click="prenota({{ $riga['turno']->id_turno }})"
                @elseif ($riga['stato'] === 'own' && $riga['prenotazione']->stato !== 'confermata')
                    wire:click="apriModal({{ $riga['prenotazione']->id_prenotazione }})"
                @elseif ($riga['stato'] === 'own' && $riga['prenotazione']->stato === 'confermata')
                    wire:click="avvisaConfermata"
                @endif
            >
                <div class="check-day-turn">
                    <input type="checkbox" tabindex="-1" @checked($riga['stato'] === 'occupied' || $riga['stato'] === 'own')>
                    <span>{{ $i + 1 }}°</span>
                </div>

                <div class="time-day-turn">
                    <span>{{ substr($riga['turno']->orario_inizio, 0, 5) }} - {{ substr($riga['turno']->orario_fine, 0, 5) }}</span>
                </div>

                <div class="user-day-turn">
                    @if ($riga['stato'] === 'available')
                        <i data-lucide="plus"></i>
                    @elseif ($riga['prenotazione'])
                        <div class="horizontal-container">
                            <div class="profile-pic" style="background:#000;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;">{{ strtoupper($riga['prenotazione']->utente->nome[0]) }}</div>
                            <span>{{ $riga['prenotazione']->utente->nome[0] }}. {{ $riga['prenotazione']->utente->cognome }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($prenotazioneConfermata)
        <div id="modal-booking-success" class="full-modal showing" aria-hidden="false">
            <div class="full-modal-body">
                <div class="modal-header result-modal-header">
                    <div class="horizontal-container">
                        <i data-lucide="check"></i>
                        <span class="modal-big-title">Prenotazione effettuata con successo</span>
                    </div>
                </div>
                <div class="full-modal-content" id="booking-summary-list">
                    <div class="booking-summary-row">
                        <span class="summary-date">
                            {{ ucfirst(\Illuminate\Support\Carbon::parse($prenotazioneConfermata->data_prenotazione)->locale('it')->translatedFormat('l j F Y')) }}
                            — {{ $prenotazioneConfermata->turno->indice }}° Turno
                        </span>
                        <span class="summary-time">
                            {{ substr($prenotazioneConfermata->turno->orario_inizio, 0, 5) }} - {{ substr($prenotazioneConfermata->turno->orario_fine, 0, 5) }}
                        </span>
                    </div>

                    @if ($prenotazioneAutoConfermata)
                        <div class="info-box" style="margin-top:12px;">
                            Il turno era già iniziato da più di 30 minuti: la prenotazione è stata confermata automaticamente e non è più annullabile.
                        </div>
                    @endif
                </div>
                <div class="full-modal-footer">
                    <button type="button" class="w-text" wire:click="chiudiModalSuccesso">Chiudi</button>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.partials.modal-modifica-prenotazione')

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>