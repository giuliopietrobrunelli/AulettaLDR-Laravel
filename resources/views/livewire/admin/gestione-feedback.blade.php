<div id="gestione-feedback-wrapper">
    <div class="section-header" style="margin-bottom:16px;">
        <span class="section-title">Feedback ricevuti</span>
        <button type="button" class="w-text" wire:click="$refresh">
            <i data-lucide="refresh-cw"></i>
            <span>Aggiorna</span>
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Utente</th>
                    <th>Categoria</th>
                    <th>Contenuto</th>
                    <th>Data</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($feedback as $f)
                    <tr style="cursor:pointer;" wire:click="apriDettaglio({{ $f->id_feedback }})">
                        <td>{{ $f->utente->cognome }} {{ $f->utente->nome }}</td>
                        <td>
                            <span class="badge {{ $f->categoria === 'bug' ? 'badge-red' : ($f->categoria === 'suggerimento' ? 'badge-green' : 'badge-gray') }}">
                                {{ ucfirst($f->categoria) }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($f->contenuto, 60) }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($f->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $f->stato === 'non_gestito' ? 'badge-red' : ($f->stato === 'in_lavorazione' ? 'badge-yellow' : 'badge-gray') }}">
                                {{ ucfirst(str_replace('_', ' ', $f->stato)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Nessun feedback ricevuto.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($feedbackAperto)
        <div id="modal-feedback-dettaglio" class="full-modal showing" aria-hidden="false">
            <div class="full-modal-body">
                <div class="modal-header">
                    <span class="modal-title">Dettaglio feedback</span>
                </div>
                <div class="full-modal-content">
                    <div class="form-block">
                        <label><span>Categoria</span></label>
                        <div>
                            <span class="badge {{ $feedbackAperto->categoria === 'bug' ? 'badge-red' : ($feedbackAperto->categoria === 'suggerimento' ? 'badge-green' : 'badge-gray') }}">
                                {{ ucfirst($feedbackAperto->categoria) }}
                            </span>
                        </div>
                    </div>

                    <div class="form-block">
                        <label><span>Utente</span></label>
                        <span>{{ $feedbackAperto->utente->cognome }} {{ $feedbackAperto->utente->nome }} — n.{{ $feedbackAperto->utente->numero_tessera }}</span>
                    </div>

                    <div class="form-block">
                        <label><span>Data invio</span></label>
                        <span>{{ \Illuminate\Support\Carbon::parse($feedbackAperto->created_at)->format('d/m/Y, H:i') }}</span>
                    </div>

                    <div class="form-block">
                        <label><span>Contenuto</span></label>
                        <textarea readonly rows="5" style="background-color:#f5f5f5;">{{ $feedbackAperto->contenuto }}</textarea>
                    </div>

                    <div class="form-block">
                        <label><span>Stato</span></label>
                        <select wire:model="statoModal">
                            <option value="non_gestito">Non gestito</option>
                            <option value="in_lavorazione">In lavorazione</option>
                            <option value="gestito">Gestito</option>
                        </select>
                    </div>
                </div>
                <div class="full-modal-footer">
                    <button type="button" class="w-text danger" wire:click="elimina" onclick="return confirm('Eliminare questo feedback?')" style="margin-right:auto;">
                        <i data-lucide="trash-2"></i>
                        <span>Elimina</span>
                    </button>
                    <button type="button" class="w-text" wire:click="chiudiDettaglio">Chiudi</button>
                    <button type="button" class="w-text active" wire:click="salvaStato">
                        <i data-lucide="save"></i>
                        <span>Salva stato</span>
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