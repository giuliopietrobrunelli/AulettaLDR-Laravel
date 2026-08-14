<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Registrati tramite dati
    </x-slot>

    <form id="register-by-name-form" novalidate method="POST" action="{{ route('register') }}">
        @csrf

        <input type="hidden" name="registration_type" value="alternative">

        <section class="form-block horizontal">
            <h1>Registrati tramite i tuoi dati</h1>
        </section>

        <x-validation-errors class="mb-4" />

        <section class="form-block">
            <div class="form-block">
                <label for="nome"><span>Nome</span></label>
                <input 
                    id="nome" 
                    name="nome" 
                    type="text" 
                    placeholder="Es. Mario" 
                    value="{{ old('nome') }}"
                    autocomplete="off"
                    pattern="[a-zA-ZàèéìòùÀÈÉÌÒÙáéíóúÁÉÍÓÚ\s'-]*"
                    oninput="this.value=this.value.replace(/[^a-zA-ZàèéìòùÀÈÉÌÒÙáéíóúÁÉÍÓÚ\s'-]/g,'')" 
                    required
                    autofocus
                >
            </div>

            <div class="form-block">
                <label for="cognome"><span>Cognome</span></label>
                <input 
                    id="cognome" 
                    name="cognome" 
                    type="text" 
                    placeholder="Es. Rossi" 
                    value="{{ old('cognome') }}"
                    autocomplete="off"
                    pattern="[a-zA-ZàèéìòùÀÈÉÌÒÙáéíóúÁÉÍÓÚ\s'-]*"
                    oninput="this.value=this.value.replace(/[^a-zA-ZàèéìòùÀÈÉÌÒÙáéíóúÁÉÍÓÚ\s'-]/g,'')" 
                    required
                >
            </div>
        </section>

        <section class="form-block">
            <button type="submit" class="active">Ricevi codice via mail</button>

            <h3>Problemi con la registrazione? <a href="mailto:illumedellaragione6@gmail.com">Contatta il direttivo</a></h3>

            <div class="divider"></div>

            <h3>Un altro modo per registrarsi?</h3>
            <a href="{{ route('register') }}">
                <button type="button">Registrati tramite tessera LDR</button>
            </a>

            <div class="divider"></div>

            <h3>Hai già un account?</h3>
            <a href="{{ route('login') }}">
                <button type="button">Accedi</button>
            </a>
        </section>
    </form>
</x-guest-layout>