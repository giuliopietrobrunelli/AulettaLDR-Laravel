<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Imposta la password
    </x-slot>

    <form id="set-password-form" novalidate method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ request('email', $request->email) }}">

        <section class="form-block horizontal">
            <h1>Ci sei quasi, imposta la tua password</h1>
        </section>

        <x-validation-errors class="mb-4" />

        <section class="form-block">
            <div class="form-block">
                <label for="password"><span>Nuova password</span></label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    placeholder="Almeno 8 caratteri" 
                    autocomplete="new-password" 
                    minlength="8" 
                    required
                    autofocus
                >
            </div>

            <div class="form-block">
                <label for="password_confirmation"><span>Conferma password</span></label>
                <input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    placeholder="Ripeti la password" 
                    autocomplete="new-password" 
                    minlength="8" 
                    required
                >
            </div>
        </section>

        <section class="form-block">
            <button type="submit" class="active">Salva nuova password e accedi</button>
        </section>

    </form>
</x-guest-layout>