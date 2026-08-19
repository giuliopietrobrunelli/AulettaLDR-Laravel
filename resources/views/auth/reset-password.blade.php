{{-- resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>
    <x-slot name="title">
        Auletta LDR — Imposta una nuova password
    </x-slot>

    <form id="reset-password-form" novalidate method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <section class="form-block horizontal">
            <h1>Imposta una nuova password</h1>
        </section>

        <x-validation-errors class="mb-4" />

        <section class="form-block">
            <div class="form-block">
                <label for="email"><span>Email</span></label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $request->email) }}"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-block">
                <label for="password"><span>Nuova password</span></label>
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
                <label for="password_confirmation"><span>Conferma nuova password</span></label>
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
            <button type="submit" class="active">Reimposta password</button>
        </section>
    </form>
</x-guest-layout>