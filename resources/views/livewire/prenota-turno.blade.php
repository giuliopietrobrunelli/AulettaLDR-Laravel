<div id="modal-prenota" class="full-modal" wire:ignore.self>
    <div class="full-modal-body">
        <div class="modal-header">
            <span class="modal-title">Prenota l'auletta</span>
            <span class="modal-subtitle">Seleziona data e turno</span>
        </div>
        <div class="full-modal-content">
            <div class="form-block">

                <div class="form-block">
                    <label for="prenota-data"><span>Data</span></label>
                    <input
                        id="prenota-data"
                        type="date"
                        wire:model.live="data"
                        min="{{ $dataMinima }}"
                        max="{{ $dataMassima }}">
                </div>

                <div class="form-block">
                    <label for="prenota-turno"><span>Turno</span></label>
                    <select id="prenota-turno" wire:model.live="idTurno">
                        <option value="" selected>Seleziona un orario</option>
                        @foreach ($turni as $turno)
                        <option value="{{ $turno->id_turno }}" @disabled($turno->occupato || $turno->terminato)>
                            {{ $turno->indice }}° ({{ substr($turno->orario_inizio, 0, 5) }}-{{ substr($turno->orario_fine, 0, 5) }})
                            @if($turno->occupato) — Occupato @elseif($turno->terminato) — Terminato @endif
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
        <div class="full-modal-footer">
            <button type="button" class="w-text" data-modal="close-modal">Annulla</button>
            <button type="button" class="w-text active" wire:click="salva" @disabled(!$idTurno)>
                <i data-lucide="check"></i>
                <span>Conferma prenotazione</span>
            </button>
        </div>
    </div>

    @script
    <script>
        Livewire.on('close-modal-prenota', () => {
            window.modal?.closeEl(document.getElementById('modal-prenota'));
        });
    </script>
    @endscript
</div>