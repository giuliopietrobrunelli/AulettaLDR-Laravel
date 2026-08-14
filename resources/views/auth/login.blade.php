<x-guest-layout>

    <x-slot name="title">
        Auletta LDR — Effettua l'accesso
    </x-slot>

    <form id="login-form" novalidate method="POST" action="{{ route('login') }}">
        @csrf

        <section class="form-block horizontal">
            <img src="{{ asset('img/logo-prenotaldr-giallo.webp') }}" alt="Logo Auletta LDR">
            <h1>Accedi per prenotare l'auletta</h1>
        </section>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 text-sm text-green-600" style="color: #65c45c;">
                {{ session('status') }}
            </div>
        @endif

        <section class="form-block">
            <div class="form-block">
                <label for="email"><span>Email / Numero tessera</span></label>
                <input 
                    id="email" 
                    name="email" 
                    type="text" 
                    placeholder="tuamail@esempio.it o 0000" 
                    value="{{ old('email') }}" 
                    autocomplete="username" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-block">
                <label for="password"><span>Password</span></label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    placeholder="••••••••" 
                    autocomplete="current-password" 
                    required
                >
            </div>

            <div class="horizontal-container" style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size: 13px; text-decoration: underline;">
                        Password dimenticata? Reimposta la password
                    </a>
                @endif
            </div>
        </section>

        <section class="form-block">
            <button type="submit" class="active">Effettua l'accesso</button>

            <div class="divider"></div>

            <h3>Non hai ancora un account?</h3>
            <a href="{{ route('register') }}">
                <button type="button">Crea un account</button>
            </a>
        </section>
    </form>

</x-guest-layout>
