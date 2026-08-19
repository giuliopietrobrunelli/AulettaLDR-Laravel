{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Recupera la password
    </x-slot>

    <form id="reset-password-form" novalidate method="POST" action="{{ route('password.email') }}">
        @csrf

        <section class="form-block horizontal">
            <h1>Recupera la password</h1>
        </section>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600" style="color: #65c45c;">
                {{ session('status') }}
            </div>
        @endif

        <section class="form-block">
            <p>Inserisci l'email associata al tuo account: ti invieremo un link per reimpostare la password.</p>

            <div class="form-block">
                <label for="email"><span>Email</span></label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="tuamail@esempio.it"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>
        </section>

        <section class="form-block">
            <button type="submit" class="active">Invia link di reset</button>

            <div class="divider"></div>

            <h3>Ti sei ricordato la password?</h3>
            <a href="{{ route('login') }}">
                <button type="button">Torna al login</button>
            </a>
        </section>
    </form>
</x-guest-layout>