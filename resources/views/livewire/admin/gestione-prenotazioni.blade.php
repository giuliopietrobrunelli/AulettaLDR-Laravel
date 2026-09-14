<div id="gestione-prenotazioni-wrapper">
    <span class="section-title" style="display:block;margin-bottom:16px;">Prenotazioni</span>

    <div class="form-section">
        <div class="section-header">
            <span class="section-title" style="font-size:15px;">Prenotazione privilegiata</span>
            <button type="button" class="w-text" wire:click="apriPrenotazionePrivilegiata">
                <i data-lucide="calendar-plus"></i>
                <span>Prenota per utente</span>
            </button>
        </div>
        <div class="info-box">
            Come amministratore puoi prenotare un turno per qualsiasi utente, senza limitazioni di data o settimana.
        </div>
    </div>

    <div class="form-section" style="margin-top:24px;">
        <div class="section-header">
            <span class="section-title" style="font-size:15px; white-space: nowrap;">Tutte le prenotazioni</span>
            <input type="text" wire:model.live.debounce.400ms="ricerca" placeholder="Cerca utente"
                style="max-width: 240px;">
        </div>

        <div class="tab-bar" style="margin-top:16px;margin-bottom:16px;">
            <button type="button" class="tab-btn {{ $filtroPeriodo === 'future' ? 'active' : '' }}"
                wire:click="cambiaFiltroPeriodo('future')">
                Prenotazioni future
            </button>
            <button type="button" class="tab-btn {{ $filtroPeriodo === 'passate' ? 'active' : '' }}"
                wire:click="cambiaFiltroPeriodo('passate')">
                Prenotazioni passate
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Utente</th>
                        <th>Data</th>
                        <th>Turno</th>
                        <th>Stato</th>
                        <th>Creata il</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prenotazioni as $prenotazione)
                        <tr>
                            <td>{{ $prenotazione->utente->cognome }} {{ $prenotazione->utente->nome }}</td>
                            <td>{{ Carbon\Carbon::parse($prenotazione->data_prenotazione)->format('d/m/Y') }}</td>
                            <td>{{ $prenotazione->turno->indice }}°</td>
                            <td>
                                <span
                                    class="badge {{ $prenotazione->stato === 'confermata' ? 'badge-green' : ($prenotazione->stato === 'riservata' ? 'badge-blue' : 'badge-yellow') }}">
                                    {{ ucfirst(str_replace('_', ' ', $prenotazione->stato)) }}
                                </span>
                            </td>
                            <td>{{ Carbon\Carbon::parse($prenotazione->data_creazione_prenotazione)->format('d/m/Y') }}</td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="btn-icon"
                                        wire:click="apriModifica({{ $prenotazione->id_prenotazione }})" title="Modifica">
                                        <i data-lucide="pencil"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6">Nessuna prenotazione trovata.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $prenotazioni->links('vendor.pagination.custom') }}</div>
    </div>

    @if ($modaleAperto || $modalePrivilegiataAperto)
        <div id="modal-prenota-admin" class="full-modal showing" aria-hidden="false">
            <div class="full-modal-body">
                <div class="modal-header">
                    <span class="modal-title">
                        {{ $modificaPrenotazioneId ? 'Modifica prenotazione' : 'Prenota per utente' }}
                    </span>
                </div>
                <div class="full-modal-content">
                    <div class="form-block">
                        <label><span>Data</span></label>
                        <input type="date" wire:model="dataPrenotazione">
                        @error('dataPrenotazione') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Turno</span></label>
                        <select wire:model="idTurno">
                            <option value="">Seleziona turno</option>
                            @foreach ($turni as $turno)
                                <option value="{{ $turno->id_turno }}">{{ $turno->indice }}°
                                    ({{ substr($turno->orario_inizio, 0, 5) }}-{{ substr($turno->orario_fine, 0, 5) }})</option>
                            @endforeach
                        </select>
                        @error('idTurno') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Utente</span></label>
                        <select wire:model="idUtente">
                            <option value="">Seleziona utente</option>
                            @foreach ($utenti as $utente)
                                <option value="{{ $utente->id_utente }}">{{ $utente->cognome }} {{ $utente->nome }}
                                    (n.{{ $utente->numero_tessera }})</option>
                            @endforeach
                        </select>
                        @error('idUtente') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Stato</span></label>
                        <select wire:model="stato">
                            <option value="non_confermata">Non confermata</option>
                            <option value="confermata">Confermata</option>
                            <option value="riservata">Riservata</option>
                        </select>
                    </div>
                </div>
                <div class="full-modal-footer">
                    @if ($modificaPrenotazioneId)
                        <button type="button" class="w-text danger" wire:click="elimina"
                            onclick="return confirm('Eliminare questa prenotazione?')" style="margin-right:auto;">
                            <i data-lucide="trash-2"></i>
                            <span>Elimina</span>
                        </button>
                    @endif
                    <button type="button" class="w-text" wire:click="chiudiModale">Annulla</button>
                    <button type="button" class="w-text active" wire:click="salva">
                        <i data-lucide="save"></i>
                        <span>Salva</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
        Livewire.on('modal-aperto', () => {
            setTimeout(() => lucide.createIcons(), 50);
        });
    </script>
    @endscript
</div>