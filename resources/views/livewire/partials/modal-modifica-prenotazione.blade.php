@if ($prenotazioneModal)
    <div id="modal-modifica-prenotazione" class="full-modal showing" aria-hidden="false">
        <div class="full-modal-body">
            <div class="modal-header">
                <span class="modal-title">Modifica la prenotazione</span>
                <span class="modal-subtitle">Dettagli del turno e opzioni di modifica</span>
            </div>

            <div class="form-section">
                <div class="form-block">
                    <label><span>Data:</span></label>
                    <input type="text" readonly value="{{ ucfirst(\Illuminate\Support\Carbon::parse($prenotazioneModal->data_prenotazione)->locale('it')->translatedFormat('l j F Y')) }}" style="pointer-events:none;background-color:#f5f5f5;">
                </div>
                <div class="form-block">
                    <label><span>Turno:</span></label>
                    <input type="text" readonly value="{{ $prenotazioneModal->turno->indice }}° Turno — {{ substr($prenotazioneModal->turno->orario_inizio, 0, 5) }} - {{ substr($prenotazioneModal->turno->orario_fine, 0, 5) }}" style="pointer-events:none;background-color:#f5f5f5;">
                </div>
            </div>

            @if (in_array($prenotazioneModal->stato, ['confermata', 'non_confermata']))
                <div class="form-section">
                    <div class="form-block">
                        <label><span>Cedi Turno a:</span></label>
                        <select wire:model="destinatarioId">
                            <option value="">Seleziona utente</option>
                            @foreach ($utentiCedibili as $u)
                                <option value="{{ $u->id_utente }}">{{ $u->cognome }} {{ $u->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="w-text action-btn" wire:click="cediTurno" @disabled(!$destinatarioId)>
                        <i data-lucide="handshake"></i> Cedi Turno
                    </button>
                </div>
            @endif

            @if ($prenotazioneModal->stato !== 'confermata')
                <div class="form-section">
                    <label><span>Rinuncia al tuo turno</span></label>
                    <button type="button" class="w-text danger" wire:click="rinunciaTurno">
                        <i data-lucide="calendar-off"></i> Rinuncia Turno
                    </button>
                </div>
            @endif

            <div class="full-modal-footer">
                <button type="button" class="w-text button-secondary" wire:click="chiudiModal">Annulla</button>
            </div>
        </div>
    </div>
@endif