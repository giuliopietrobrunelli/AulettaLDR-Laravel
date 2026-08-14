<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Registrati tramite numero tessera
    </x-slot>

    <form id="register-form" novalidate method="POST" action="{{ route('register') }}">
        @csrf

        <section class="form-block horizontal">
            <h1>Registrati tramite la tua tessera LDR</h1>
        </section>

        <x-validation-errors class="mb-4" />

        <section class="form-block">
            <div class="form-block">
                <label for="n-tessera"><span>Numero tessera</span></label>
                <input 
                    id="n-tessera" 
                    name="n_tessera" 
                    type="number" 
                    placeholder="0000" 
                    pattern="\d*" 
                    min="1"
                    oninput="this.value=this.value.replace(/\D/g,'')" 
                    value="{{ old('n_tessera') }}"
                    autocomplete="off" 
                    required
                    autofocus
                >
            </div>
        </section>

        <section class="form-block">
            <button type="submit" class="active">Ricevi codice via mail</button>

            <h3>Problemi con la registrazione? <a href="mailto:illumedellaragione6@gmail.com">Contatta il direttivo</a></h3>

            <div class="divider"></div>

            <h3>Non hai la tessera a portata di mano?</h3>
            <a href="{{ route('register.alternative') }}">
                <button type="button">Registrati tramite dati</button>
            </a>

            <div class="divider"></div>

            <h3>Hai già un account?</h3>
            <a href="{{ route('login') }}">
                <button type="button">Accedi</button>
            </a>
        </section>
    </form>
</x-guest-layout>
