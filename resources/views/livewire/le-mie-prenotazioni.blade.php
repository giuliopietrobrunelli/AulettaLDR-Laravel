<div id="le-mie-prenotazioni-wrapper">
    @if ($turnoCorrente)
        <div class="bookings-row">
            <div class="bookings-day-indicator">
                <span>
                    @if ($turnoCorrente->id_utente === $miaUtenteId)
                        Attualmente ti trovi in auletta:
                    @else
                        Attualmente l'auletta è occupata:
                    @endif
                </span>
            </div>

            <div class="reservation-card reservation-card-active">
                <div class="reservation-info">
                    <div class="reservation-turn-info">
                        <h2 class="semibold">{{ $turnoCorrente->turno->indice }}° Turno</h2>
                        <span>{{ substr($turnoCorrente->turno->orario_inizio, 0, 5) }} -
                            {{ substr($turnoCorrente->turno->orario_fine, 0, 5) }}</span>
                    </div>
                    <div class="reservation-meta horizontal-container">
                        <span
                            class="reservation-status {{ $turnoCorrente->stato === 'confermata' ? 'confermata' : 'non-confermata' }}">
                            {{ $turnoCorrente->stato === 'confermata' ? 'Confermata' : 'Non confermata' }}
                        </span>
                    </div>
                </div>

                <div class="horizontal-container action-container">
                    <div class="horizontal-container">
                        <div class="profile-pic"
                            style="background:#000;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;">
                            {{ strtoupper($turnoCorrente->utente->nome[0]) }}
                        </div>
                        <span>{{ $turnoCorrente->utente->nome[0] }}. {{ $turnoCorrente->utente->cognome }}</span>
                    </div>
                </div>

                @if ($turnoCorrente->id_utente === $miaUtenteId && $turnoCorrente->stato !== 'confermata')
                    <div class="horizontal-container action-container">
                        <button type="button" class="w-text"
                            wire:click="confermaPresenza({{ $turnoCorrente->id_prenotazione }})">
                            <span>Conferma presenza</span>
                        </button>
                        <button type="button" class="w-text" wire:click="apriModal({{ $turnoCorrente->id_prenotazione }})">
                            <span>Modifica</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="divider"></div>
    @endif

    @forelse ($mieFuture as $giorno => $prenotazioni)
        <div class="bookings-row">
            <div class="bookings-day-indicator">
                @php
                    $dataGiorno = \Illuminate\Support\Carbon::parse($giorno)->locale('it');
                    $labelGiorno = $dataGiorno->isToday() ? 'Oggi, ' : ($dataGiorno->isTomorrow() ? 'Domani, ' : '');
                @endphp
                <span>{{ $labelGiorno . ucfirst($dataGiorno->translatedFormat('l j F Y')) }}</span>
            </div>

            @foreach ($prenotazioni as $prenotazione)
                <div class="reservation-card">
                    <div class="reservation-info">
                        <div class="reservation-turn-info">
                            <h2 class="semibold">{{ $prenotazione->turno->indice }}° Turno</h2>
                            <span>{{ substr($prenotazione->turno->orario_inizio, 0, 5) }} -
                                {{ substr($prenotazione->turno->orario_fine, 0, 5) }}</span>
                        </div>
                        <div class="reservation-meta horizontal-container">
                            <span
                                class="reservation-status {{ $prenotazione->stato === 'confermata' ? 'confermata' : 'non-confermata' }}">
                                {{ $prenotazione->stato === 'confermata' ? 'Confermata' : 'Non confermata' }}
                            </span>
                        </div>
                    </div>

                    <div class="horizontal-container action-container">
                        <button type="button" class="w-text" wire:click="apriModal({{ $prenotazione->id_prenotazione }})">
                            <span>Modifica</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        @if (!$turnoCorrente)
            <span class="bookings-empty disabled">Nessuna prenotazione in programma.</span>
        @endif
    @endforelse

    @include('livewire.partials.modal-modifica-prenotazione')

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>