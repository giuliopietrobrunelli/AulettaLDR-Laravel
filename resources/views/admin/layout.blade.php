<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Amministratore — AulettaLDR</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        main>div {
            width: 100%;
            box-sizing: border-box;
        }

        main {
            width: 100%;
        }
    </style>
    @livewireStyles
</head>

<body>
    <header>
        <div id="logo">
            <a href="{{ route('calendario.mese') }}">
                <img src="{{ asset('img/logo-prenotaldr-giallo.webp') }}" alt="">
            </a>
            <h2>Dashboard Amministratore</h2>
        </div>
        <button type="button" class="w-text" onclick="window.location.href='{{ route('calendario.mese') }}'">
            <i data-lucide="arrow-left"></i>
            <span>Torna all'app</span>
        </button>
    </header>

    <nav class="dynamic-nav">
        <div class="horizontal-container">
            <button type="button" onclick="window.location.href='{{ route('admin.utenti') }}'"
                class="w-text nav-switch @if(request()->routeIs('admin.utenti')) active @endif">
                <i data-lucide="users"></i>
                <span>Utenti</span>
            </button>
            <button type="button" onclick="window.location.href='{{ route('admin.turni') }}'"
                class="w-text nav-switch @if(request()->routeIs('admin.turni')) active @endif">
                <i data-lucide="clock"></i>
                <span>Turni</span>
            </button>
            <button type="button" onclick="window.location.href='{{ route('admin.prenotazioni') }}'"
                class="w-text nav-switch @if(request()->routeIs('admin.prenotazioni')) active @endif">
                <i data-lucide="calendar-clock"></i>
                <span>Prenotazioni</span>
            </button>
            <button type="button" onclick="window.location.href='{{ route('admin.statistiche') }}'"
                class="w-text nav-switch @if(request()->routeIs('admin.statistiche')) active @endif">
                <i data-lucide="bar-chart-2"></i>
                <span>Statistiche</span>
            </button>
            <button type="button" onclick="window.location.href='{{ route('admin.feedback') }}'"
                class="w-text nav-switch @if(request()->routeIs('admin.feedback')) active @endif">
                <i data-lucide="megaphone"></i>
                <span>Feedback</span>
            </button>
            <button type="button" onclick="window.location.href='{{ route('admin.impostazioni') }}'"
                class="w-text nav-switch @if(request()->routeIs('admin.impostazioni')) active @endif">
                <i data-lucide="settings"></i>
                <span>Impostazioni</span>
            </button>
        </div>
    </nav>

    <main>
        @if(request()->routeIs('admin.turni'))
            <livewire:admin.gestione-turni />
        @endif
        @if(request()->routeIs('admin.utenti'))
            <livewire:admin.gestione-utenti />
        @endif
        @if(request()->routeIs('admin.prenotazioni'))
            <livewire:admin.gestione-prenotazioni />
        @endif
        @if(request()->routeIs('admin.feedback'))
            <livewire:admin.gestione-feedback />
        @endif
        @if(request()->routeIs('admin.impostazioni'))
            <livewire:admin.gestione-impostazioni />
        @endif
        @if(request()->routeIs('admin.statistiche'))
            <livewire:admin.statistiche />
        @endif
    </main>
    
    <footer>
        <h5>© 2010-2026 Il Lume della Ragione — Dashboard amministratori</h5>

        <div class="horizontal-container">
            <span style="font-style: italic; opacity: 0.6; font-size: 13px;">
                Connesso come {{ auth()->user()->utente->nome }} {{ auth()->user()->utente->cognome }}
            </span>
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    @livewireScripts

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('toast', (event) => {
                window.showToast(event.tipo, event.messaggio, 'circle-check');
            });
        });
    </script>
    @vite(['resources/js/admin-toast.js'])
</body>

</html>