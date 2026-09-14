<div id="gestione-impostazioni-wrapper">
    <span class="section-title" style="display:block;margin-bottom:16px;">Impostazioni</span>

    <div class="settings-grid">
        <div class="setting-row">
            <label for="limite-settimanale">Limite prenotazioni settimanali</label>
            <input type="number" id="limite-settimanale" wire:model="limiteSettimanale" min="1">
            <span class="setting-desc">Numero massimo di prenotazioni che un utente può effettuare nella stessa settimana.</span>
            @error('limiteSettimanale') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div class="setting-row">
            <label for="settimane-anticipo">Settimane di anticipo</label>
            <input type="number" id="settimane-anticipo" wire:model="settimaneAnticipo" min="0">
            <span class="setting-desc">Quante settimane prima della fine del mese diventa visibile il mese successivo per le prenotazioni.</span>
            @error('settimaneAnticipo') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="button" class="w-text active" wire:click="salva">
                <i data-lucide="save"></i>
                <span>Salva impostazioni</span>
            </button>
        </div>
    </div>

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>