<div id="statistiche-wrapper">
    <div class="section-header" style="margin-bottom:16px;">
        <span class="section-title">Statistiche</span>
        <button type="button" class="w-text" wire:click="$refresh">
            <i data-lucide="refresh-cw"></i>
            <span>Aggiorna</span>
        </button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-card-label">Utenti registrati</span>
            <span class="stat-card-value">{{ $dati['utenti_attivi'] }}</span>
            <span class="stat-card-sub">con accesso attivo</span>
        </div>

        <div class="stat-card">
            <span class="stat-card-label">Utenti totali</span>
            <span class="stat-card-value">{{ $dati['utenti_totali'] }}</span>
            <span class="stat-card-sub">nel database</span>
        </div>

        <div class="stat-card">
            <span class="stat-card-label">Prenotazioni mese</span>
            <span class="stat-card-value">{{ $dati['prenotazioni_mese_corrente'] }}</span>
            <span class="stat-card-sub">{{ \Illuminate\Support\Carbon::now()->locale('it')->translatedFormat('F Y') }}</span>
        </div>

        <div class="stat-card">
            <span class="stat-card-label">Prenotazioni oggi</span>
            <span class="stat-card-value">{{ $dati['prenotazioni_oggi'] }}</span>
            <span class="stat-card-sub">turni occupati</span>
        </div>

        <div class="stat-card">
            <span class="stat-card-label">Tasso conferma</span>
            <span class="stat-card-value">{{ $dati['tasso_conferma'] }}%</span>
            <span class="stat-card-sub">confermate / totali</span>
        </div>

        <div class="stat-card">
            <span class="stat-card-label">Limite settimanale</span>
            <span class="stat-card-value">{{ $dati['limite_settimanale'] }}</span>
            <span class="stat-card-sub">prenotazioni/settimana</span>
        </div>
    </div>

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>