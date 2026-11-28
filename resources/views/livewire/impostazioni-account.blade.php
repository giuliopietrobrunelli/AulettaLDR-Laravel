<div id="account-settings">
    @if (session('message'))
        <div class="toast-success">{{ session('message') }}</div>
    @endif

    <div class="form-section">
        <h2>Il tuo profilo</h2>

        <div class="form-block">
            <label><span>Nome</span></label>
            <input type="text" readonly value="{{ $utente->nome }} {{ $utente->cognome }}" style="pointer-events:none;background-color:#f5f5f5;">
        </div>

        <div class="form-block">
            <label><span>Numero tessera</span></label>
            <input type="text" readonly value="{{ $utente->numero_tessera }}" style="pointer-events:none;background-color:#f5f5f5;">
        </div>

        <div class="form-block">
            <label><span>Email</span></label>
            <input type="text" readonly value="{{ $utente->email }}" style="pointer-events:none;background-color:#f5f5f5;">
        </div>

        <div class="form-block">
            <label><span>Telefono</span></label>
            <input type="text" readonly value="{{ $utente->telefono ?? '—' }}" style="pointer-events:none;background-color:#f5f5f5;">
        </div>

        <div class="form-block">
            <label><span>Facoltà</span></label>
            <input type="text" readonly value="{{ $utente->facolta_universitaria ?? '—' }}" style="pointer-events:none;background-color:#f5f5f5;">
        </div>

        <span style="font-size:12px;opacity:0.6;">Per modificare questi dati contatta un amministratore.</span>
    </div>

</div>