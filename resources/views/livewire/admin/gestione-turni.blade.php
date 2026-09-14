<div id="gestione-turni-wrapper">
    <div class="form-section">
        <div class="section-header">
            <span class="section-title">Gestione turni</span>
            <button type="button" class="w-text" wire:click="apriCreazione">
                <i data-lucide="plus"></i>
                <span>Nuovo turno</span>
            </button>
        </div>

        <div class="info-box">
            I turni definiscono le fasce orarie prenotabili dagli utenti. Modifica o disattiva un turno con attenzione: le prenotazioni esistenti non vengono cancellate automaticamente.
        </div>

        <div class="turni-list">
            @foreach ($turni as $turno)
                <div class="turno-row">
                    <span class="turno-index">{{ $turno->indice }}</span>
                    <span class="turno-label">{{ $turno->indice }}° Turno</span>
                    <span class="turno-time">{{ substr($turno->orario_inizio, 0, 5) }} – {{ substr($turno->orario_fine, 0, 5) }}</span>

                    <span class="badge {{ $turno->attivo ? 'badge-green' : 'badge-gray' }}">
                        {{ $turno->attivo ? 'Attivo' : 'Disattivo' }}
                    </span>

                    <div class="table-actions">
                        <button type="button" class="btn-icon" wire:click="apriModifica({{ $turno->id_turno }})" title="Modifica">
                            <i data-lucide="pencil"></i>
                        </button>
                        <button type="button" class="btn-icon" wire:click="toggleAttivo({{ $turno->id_turno }})" title="{{ $turno->attivo ? 'Disattiva' : 'Riattiva' }}">
                            <i data-lucide="{{ $turno->attivo ? 'ban' : 'check' }}"></i>
                        </button>
                        <button type="button" class="btn-icon" wire:click="elimina({{ $turno->id_turno }})" onclick="return confirm('Eliminare definitivamente questo turno e tutte le sue prenotazioni?')" title="Elimina">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($modaleApertoCreazione)
        <div id="modal-turno" class="full-modal showing" aria-hidden="false">
            <div class="full-modal-body">
                <div class="modal-header">
                    <span class="modal-title">{{ $modificaTurnoId ? 'Modifica turno' : 'Nuovo turno' }}</span>
                </div>
                <div class="full-modal-content">
                    <div class="form-block">
                        <label><span>Orario inizio</span></label>
                        <input type="time" wire:model="orarioInizio">
                        @error('orarioInizio') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Orario fine</span></label>
                        <input type="time" wire:model="orarioFine">
                        @error('orarioFine') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="full-modal-footer">
                    <button type="button" class="w-text" wire:click="chiudiModale">Annulla</button>
                    <button type="button" class="w-text active" wire:click="salva">Salva</button>
                </div>
            </div>
        </div>
    @endif

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>