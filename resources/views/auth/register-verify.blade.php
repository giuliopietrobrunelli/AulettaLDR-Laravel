{{-- resources/views/auth/register-verify.blade.php --}}
<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Verifica il codice
    </x-slot>

    <form id="set-password-form" novalidate method="POST" action="{{ route('register.verify.store') }}">
        @csrf

        <section class="form-block horizontal">
            <h1>Inserisci il codice ricevuto via email</h1>
        </section>

        <x-validation-errors class="mb-4" />

        <section class="form-block">
            <p>Abbiamo inviato un codice a <strong>{{ $emailMasked }}</strong>.<br> Il codice è valido per 5 minuti.</p>

            <div class="form-block">
                <label for="code"><span>Codice di verifica</span></label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    pattern="\d*"
                    maxlength="6"
                    placeholder="000000"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    autocomplete="one-time-code"
                    required
                    autofocus
                >
            </div>

            <div class="form-block">
                <label for="password"><span>Scegli una password</span></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="form-block">
                <label for="password_confirmation"><span>Conferma password</span></label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    placeholder="••••••••"
                    autocomplete="new-password"
                    required
                >
            </div>
        </section>

        <section class="form-block">
            <button type="submit" class="active">Completa la registrazione</button>

            <h3>Non hai ricevuto il codice? <a href="mailto:illumedellaragione6@gmail.com">Contatta il direttivo</a></h3>

            <div class="divider"></div>

            <h3>Hai sbagliato tessera o dati?</h3>
            <a href="{{ route('register') }}">
                <button type="button">Ricomincia la registrazione</button>
            </a>
        </section>
    </form>
</x-guest-layout>