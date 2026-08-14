<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Reimposta la tua password
    </x-slot>

    <form id="reset-password-form" novalidate method="POST" action="{{ route('password.email') }}">
        @csrf

        <section class="form-block horizontal">
            <h1>Reimposta la tua password</h1>
        </section>

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-green-600" style="color: #65c45c; margin-bottom: 15px;">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <section class="form-block">
            <div class="form-block">
                <label for="email"><span>Email / Numero tessera</span></label>
                <input 
                    id="email" 
                    name="email" 
                    type="text" 
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
            <a href="{{ route('login') }}">
                <button type="button">Torna al login</button>
            </a>
        </section>
    </form>
</x-guest-layout>