<div id="gestione-utenti-wrapper">
    <div class="section-header">
        <span class="section-title">Gestione utenti</span>
        <div class="horizontal-container">
            <input type="text" wire:model.live.debounce.400ms="ricerca"
                placeholder="Cerca per nome, cognome, email, tessera..." style="margin-right: 10px;">
            <button type="button" class="w-text" wire:click="apriCreazione" style="white-space: nowrap;">
                <i data-lucide="plus"></i>
                <span>Nuovo utente</span>
            </button>
        </div>
    </div>

    <div class="tab-bar">
        <button type="button" class="tab-btn {{ $filtroStato === 'registrati' ? 'active' : '' }}"
            wire:click="$set('filtroStato', 'registrati')">
            Registrati
        </button>
        <button type="button" class="tab-btn {{ $filtroStato === 'non_registrati' ? 'active' : '' }}"
            wire:click="$set('filtroStato', 'non_registrati')">
            Non registrati
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tessera</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Facoltà</th>
                    <th>Cauzione</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($utenti as $utente)
                    <tr>
                        <td>{{ $utente->numero_tessera }}</td>
                        <td>{{ $utente->nome }} {{ $utente->cognome }}</td>
                        <td>{{ $utente->email }}</td>
                        <td>{{ $utente->telefono ?? '—' }}</td>
                        <td>{{ $utente->facolta_universitaria ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $utente->cauzione ? 'badge-green' : 'badge-red' }}">
                                {{ $utente->cauzione ? 'Sì' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon" wire:click="apriModifica({{ $utente->id_utente }})"
                                    title="Modifica">
                                    <i data-lucide="pencil"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="8">Nessun utente trovato.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $utenti->links('vendor.pagination.custom') }}</div>

    @if ($modaleAperto)
        <div id="{{ $modificaUtenteId ? 'modal-modifica-utente' : 'modal-nuovo-utente' }}" class="full-modal showing"
            aria-hidden="false">
            <div class="full-modal-body">
                <div class="modal-header">
                    <span class="modal-title">{{ $modificaUtenteId ? 'Modifica utente' : 'Nuovo utente' }}</span>
                    @if ($modificaUtenteId)
                        <span class="modal-subtitle">{{ $nome }} {{ $cognome }} — tessera n.{{ $numeroTessera }}</span>
                    @endif
                </div>
                <div class="full-modal-content">
                    @unless ($modificaUtenteId)
                        <div class="form-block">
                            <label><span>Nome</span></label>
                            <input type="text" wire:model="nome">
                            @error('nome') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-block">
                            <label><span>Cognome</span></label>
                            <input type="text" wire:model="cognome">
                            @error('cognome') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                        </div>
                    @endunless

                    <div class="form-block">
                        <label><span>Email</span></label>
                        <input type="email" wire:model="email">
                        @error('email') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Numero tessera</span></label>
                        <input type="text" wire:model="numeroTessera">
                        @error('numeroTessera') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Telefono</span></label>
                        <input type="text" wire:model="telefono">
                        @error('telefono') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-block">
                        <label><span>Facoltà</span></label>
                        <input type="text" wire:model="facoltaUniversitaria">
                    </div>
                    <div class="form-block">
                        <label><input type="checkbox" wire:model="cauzione"> <span>Cauzione versata</span></label>
                    </div>
                    <div class="form-block">
                        <label><input type="checkbox" wire:model="trattamentoDati"> <span>Trattamento dati
                                accettato</span></label>
                    </div>
                </div>
                <div class="full-modal-footer">
                    @if ($modificaUtenteId)
                        <button type="button" class="w-text danger" wire:click="elimina"
                            onclick="return confirm('Eliminare definitivamente questo utente? Verranno eliminate anche tutte le sue prenotazioni.')"
                            style="margin-right: auto;">
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