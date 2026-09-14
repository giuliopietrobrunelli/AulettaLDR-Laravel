<div id="menu-account-wrapper">
    <button data-modal="account" class="no-style" title="Impostazioni account e profilo">
        <div class="profile-pic" style="background:#000;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;width:32px;height:32px;">
            {{ strtoupper($utente->nome[0] ?? '?') }}
        </div>
    </button>

    <div id="modal-account" class="modal">
        <div class="modal-header">
            <span class="modal-title">{{ $utente->nome }} {{ $utente->cognome }}</span>
            <span class="modal-subtitle">n.{{ $utente->numero_tessera }}</span>
        </div>
        <div class="lista-notifiche">
            <span class="modal-option" onclick="window.location.href='{{ route('impostazioni') }}'" style="cursor:pointer">
                <i data-lucide="settings"></i> Impostazioni
            </span>

            @if ($isAdmin)
                <span class="modal-option" onclick="window.location.href='{{ route('admin.utenti') }}'" style="cursor:pointer">
                    <i data-lucide="layout-dashboard"></i> Dashboard amministratore
                </span>
            @endif

            <span class="modal-option" onclick="document.getElementById('logout-form').submit()" style="cursor:pointer">
                <i data-lucide="log-out"></i> Effettua Log-out
            </span>
        </div>
    </div>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
    </form>

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>