<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Auletta LDR — prenota l'auletta</title>

    <!-- manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-prenotaldr-giallo.webp') }}">

    <!-- fogli di stile -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <!-- favicon -->
    <link rel="icon" type="image/webp" href="{{ asset('img/logo-prenotaldr-giallo.webp') }}">

    <!-- font da googlefonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <!--  -->

    @livewireStyles
</head>

<body>

    <!-- header fisso della pagina -->
    <header>

        <!-- logo e nome webapp -->
        <div id="logo">
            <div class="horizontal-container">
                <a href="/">
                    <img src="{{ asset('img/logo-prenotaldr-giallo.webp') }}" alt="">
                </a>
                <h2 id="saluto-header">Ciao <span class="italic">{{ auth()->user()->utente?->nome }}</span>, al momento
                    l'auletta
                    <livewire:aulettastato />
                </h2>
            </div>
        </div>

        <!-- container azioni header -->
        <div id="desktop-header" class="horizontal-container">

            <!-- guida -->
            <button class="w-text" onclick="location.href='/guida.html'" title="Guida all'uso">
                <i data-lucide="circle-question-mark"></i>
            </button>

            <!-- feedback -->
            <button type="button" class="w-text" onclick="Livewire.dispatch('apriModalFeedback')"
                title="Invia un feedback">
                <i data-lucide="megaphone"></i>
                <span>Lascia un feedback</span>
            </button>

            <!-- prenotazioni -->
            <livewire:riepilogo-settimanale />

            <!-- inbox -->
            <button data-modal="notifiche" class="notification-button" title="Le tue notifiche">
                <i data-lucide="inbox"></i>
                <span class="notification-dot hidden"></span>
            </button>

            <!-- profilo utente -->
            <livewire:menu-account />

        </div>

        <div id="mobile-header" class="horizontal-container">

            <!-- prenotazioni -->
            <livewire:riepilogo-settimanale />

            <!-- inbox -->
            <button data-modal="notifiche" class="notification-button">
                <i data-lucide="inbox"></i>
                <span class="notification-dot hidden"></span>
            </button>

            <!-- menu -->
            <button data-modal="menu">
                <i data-lucide="menu"></i>
            </button>

        </div>

        <!-- modali dell'header (messe qui per questioni di UI) -->
        <div class="header-modals">

            <!-- notifiche -->
            <div id="modal-notifiche" class="modal">
                <div class="modal-header">
                    <span class="modal-title">Notifiche</span>
                </div>
                <div class="lista-notifiche">
                    <span class="modal-advise take-action">
                    </span>
                </div>
            </div>

        </div>

    </header>

    <!-- navbar dinamica -->
    <nav class="dynamic-nav">

        <!-- barra navigazione mobile -->

        <div class="horizontal-container">

            <div id="back-to-bar" class="back-to-bar @if(request()->routeIs('calendario.giorno')) is-visible @endif"
                aria-hidden="{{ request()->routeIs('calendario.giorno') ? 'false' : 'true' }}">
                @if(request()->routeIs('calendario.giorno'))
                    <button type="button" onclick="window.location.href='{{ route('calendario.mese') }}'"
                        class="mini active" aria-label="Torna indietro">
                        <i data-lucide="arrow-left"></i>
                    </button>
                @endif
            </div>

            <button type="button" onclick="window.location.href='{{ route('calendario.mese') }}'"
                class="w-text nav-switch @if(request()->routeIs('calendario.mese') || request()->routeIs('calendario.giorno')) active @endif">
                <i data-lucide="calendar-days"></i>
                <span>Calendario</span>
            </button>

            <button type="button" onclick="window.location.href='{{ route('prenotazioni') }}'"
                class="w-text nav-switch @if(request()->routeIs('prenotazioni')) active @endif">
                <i data-lucide="calendar-clock"></i>
                <span>Prenotazioni</span>
            </button>

            <button type="button" onclick="window.location.href='{{ route('impostazioni') }}'"
                class="w-text nav-switch @if(request()->routeIs('impostazioni')) active @endif">
                <i data-lucide="settings"></i>
                <span>Impostazioni</span>
            </button>

        </div>

    </nav>

    <!-- contenuto dinamico -->
    <main>

        <!-- header con informazioni sul calendario -->
        <div id="calendar-header">

            <!-- azioni vista settimanale -->
            <div id="week-view-actions" class="horizontal-container hidden" data-view="week">

                <!-- naviga al giorno corrente o al mese precedente/successivo -->
                <button type="button" id="week-prev" class="mini" aria-label="Vai alla settimana precedente">
                    <i data-lucide="chevron-left"></i>
                </button>
                <button type="button" id="week-today" class="mini" aria-label="Vai al giorno attuale">
                    <span>Oggi</span>
                </button>
                <button type="button" id="week-next" class="mini" aria-label="Vai alla settimana successiva">
                    <i data-lucide="chevron-right"></i>
                </button>

                <div class="vertical-separator"></div>

                <!-- seleziona il tipo di vista del calendario -->
                <button type="button" class="w-text" data-calendar-switch="month">
                    <i data-lucide="calendar-range"></i>
                    <span>Vista settimanale</span>
                </button>

                <!-- effettua una nuova prenotazione -->
                <button type="button" class="w-text" data-modal="prenota">
                    <i data-lucide="plus"></i>
                    <span>Prenota</span>
                </button>

            </div>

            <!-- azioni vista giornaliera -->
            <div id="day-view-actions" class="horizontal-container hidden" data-view="day">

                <button type="button" id="day-prev" class="mini" aria-label="Vai al giorno precedente">
                    <i data-lucide="chevron-left"></i>
                </button>
                <button type="button" id="day-today" class="mini" aria-label="Vai al giorno attuale">
                    <span>Oggi</span>
                </button>
                <button type="button" id="day-next" class="mini" aria-label="Vai al giorno successivo">
                    <i data-lucide="chevron-right"></i>
                </button>

                <div class="vertical-separator"></div>

                <button type="button" class="w-text" data-modal="prenota">
                    <i data-lucide="plus"></i>
                    <span>Prenota</span>
                </button>

            </div>

        </div>

        <!-- calendario vista mensile -->

        @if(request()->routeIs('calendario.mese'))
            <livewire:calendario-mese />
        @endif

        <!-- vista settimanale -->

        <div id="week-calendar-container" class="hidden">

            <!-- colonne del calendario -->
            <div id="week-calendar-columns"></div>

            <div id="week-calendar-turns">
                <div id="week-schedules" class="week-calendar-day">
                </div>
            </div>

            <div id="week-view-unavailable">
                <h2>Vista settimanale non disponibile in questo formato.</h2>
                <button class="active" data-calendar-switch="month">
                    <i data-lucide="arrow-left"></i>
                    <span>Torna alla vista mensile</span>
                </button>
            </div>

        </div>

        <!-- calendario vista giornaliera -->

        @if(request()->routeIs('calendario.giorno'))
            <livewire:calendario-giorno :data="request()->route('data')" />
        @endif

        <!-- vista "le mie prenotazioni" -->
        @if(request()->routeIs('prenotazioni'))
            <livewire:le-mie-prenotazioni />
        @endif

        <!-- impostazioni account utente -->
        @if(request()->routeIs('impostazioni'))
            <livewire:impostazioni-account />
        @endif

        <!-- toast container -->
        <div class="toast-container pos-bottom-center" role="region" aria-label="notifiche">
        </div>

    </main>

    <!-- footer fisso -->
    <footer>

        <h5>© 2010-2026 Il Lume della Ragione</h5>

        <div class="horizontal-container">

            <button type="button" class="no-style" onclick="Livewire.dispatch('apriModalFeedback')">
                <h5>Feedback</h5>
            </button>

            <button class="no-style" onclick="location.href='/guida.html'">
                <h5>Guida</h5>
            </button>

        </div>

    </footer>

    <!-- elemeneti in sovra-impressione -->

    <!-- barra conferma prenotazione -->
    <div id="booking-bar" class="booking-bar hidden" aria-hidden="true">
        <div class="booking-bar-inner horizontal-container">
            <button type="button" id="booking-cancel" class="w-text">
                <span>Annulla</span>
            </button>
            <button type="button" id="booking-confirm" class="w-text active">
                <i data-lucide="notebook-pen"></i>
                <span>Conferma</span>
            </button>
        </div>

    </div>

    <!-- barra navigazione mobile -->
    <div id="navigation-bar" class="navigation-bar is-visible" aria-hidden="true">
        <div class="navigation-bar-inner horizontal-container">
            <button type="button" class="active" data-main-view="calendar">
                <i data-lucide="calendar-days"></i>
                <span>Calendario</span>
            </button>

            <button type="button" class="" data-main-view="bookings">
                <i data-lucide="calendar-clock"></i>
                <span>Prenotazioni</span>
            </button>
        </div>

    </div>

    <!-- modali a "schermo intero" -------------------------------------------------------------------------------------------------------- -->


    <!-- prenota turno -->
    <div id="modal-prenota" class="full-modal">
        <div class="full-modal-body">
            <div class="modal-header">
                <span class="modal-title">Prenota l'auletta</span>
                <span class="modal-subtitle">Seleziona data e turno</span>
            </div>
            <div class="full-modal-content">
                <form id="prenota-form" class="form-block">

                    <div class="form-block">
                        <label for="prenota-data"><span>Data</span></label>
                        <input id="prenota-data" name="data" type="date" placeholder="gg.mm.aaaa">
                    </div>

                    <div class="form-block">
                        <label for="prenota-turno"><span>Turno</span></label>
                        <select id="prenota-turno" name="turno">
                            <option value="" disabled selected>Seleziona un orario</option>
                        </select>
                    </div>

                </form>
            </div>
            <div class="full-modal-footer">
                <button type="button" class="w-text" data-modal="close-modal">Annulla</button>
                <button type="button" class="w-text active" id="btn-modal-conferma-prenota" disabled>
                    <i data-lucide="check"></i>
                    <span>Conferma prenotazione</span>
                </button>
            </div>
        </div>
    </div>

    <!-- modifica Turno -->
    <div id="modal-modifica-prenotazione" class="full-modal">
        <div class="full-modal-body">
            <div class="modal-header">
                <span class="modal-title">Modifica la prenotazione</span>
                <span class="modal-subtitle">Dettagli del turno e opzioni di modifica</span>
            </div>

            <!-- Informazioni Prenotazione -->
            <div class="form-section">
                <div class="form-block">
                    <label for="modifica-turno-data"><span>Data:</span></label>
                    <input id="modifica-turno-data" name="data" type="text" readonly="readonly" tabindex="-1"
                        style="pointer-events:none; background-color:#f5f5f5;">
                </div>

                <div class="form-block">
                    <label for="modifica-turno-orario"><span>Turno:</span></label>
                    <input id="modifica-turno-orario" name="turno" type="text" readonly="readonly" tabindex="-1"
                        style="pointer-events:none; background-color:#f5f5f5;">
                </div>
            </div>

            <!-- Azioni -->
            <div class="form-section">
                <div class="form-block">
                    <label for="modifica-turno-cedi"><span>Cedi Turno a:</span></label>
                    <select id="modifica-turno-cedi" name="cedi">
                        <option value="" selected>Seleziona utente</option>
                    </select>
                </div>
                <button type="button" class="w-text action-btn" id="btn-cedi-turno" disabled>
                    <i data-lucide="handshake"></i> Cedi Turno
                </button>
            </div>

            <!-- Bottone Rinuncia Turno -->
            <div class="form-section">
                <label for="btn-rinuncia-turno"><span>Rinuncia al tuo turno</span></label>
                <button type="button" class="w-text danger" id="btn-rinuncia-turno">
                    <i data-lucide="calendar-off"></i> Rinuncia Turno
                </button>
            </div>

            <!-- Footer Modale -->
            <div class="full-modal-footer">
                <button type="button" class="w-text button-secondary" data-modal="close-modal">Annulla</button>
            </div>
        </div>
    </div>

    <!-- prenotazione avvenuta con successo -->
    <div id="modal-booking-success" class="full-modal">
        <div class="full-modal-body">
            <div class="modal-header result-modal-header">
                <div class="horizontal-container">
                    <i data-lucide="check"></i>
                    <span class="modal-big-title">Prenotazione effettuata con successo</span>
                </div>
            </div>
            <div class="full-modal-content" id="booking-summary-list">
            </div>
            <div class="full-modal-footer">
                <button type="button" class="w-text" data-modal="close-modal">Chiudi</button>
            </div>
        </div>
    </div>

    <!-- limite prenotazioni settimanali raggiunto -->
    <div id="modal-booking-denied" class="full-modal">
        <div class="full-modal-body">
            <div class="modal-header result-modal-header">
                <div class="horizontal-container">
                    <i data-lucide="x"></i>
                    <span class="modal-big-title">Limite prenotazioni settimanali raggiunto</span>
                </div>
            </div>
            <div class="full-modal-footer">
                <button type="button" class="w-text" data-modal="close-modal">Chiudi</button>
            </div>
        </div>
    </div>

    <!-- avvertimento autoconferma turno irrevocabile -->
    <div id="modal-auto-confirm-warning" class="full-modal">
        <div class="full-modal-body">
            <div class="modal-header">
                <span class="modal-title">Attenzione</span>
                <span class="modal-subtitle">Turno in corso</span>
            </div>
            <div class="full-modal-content">
                <span id="auto-confirm-warning-text" style="font-size:13px"></span>
            </div>
            <div class="full-modal-footer">
                <button type="button" class="w-text" id="btn-auto-confirm-annulla">Annulla</button>
                <button type="button" class="w-text active" id="btn-auto-confirm-ok">
                    <i data-lucide="check" class="lucide"></i>
                    <span>Ho capito, prenota</span>
                </button>
            </div>
        </div>
    </div>

    <!-- conferma una specifica azione -->
    <div id="modal-confirm" class="full-modal">
        <div class="full-modal-body">
            <div class="modal-header">
                <span class="modal-title" id="confirm-action-title">Conferma -</span>
                <span class="modal-subtitle" id="confirm-action-subtitle"></span>
            </div>
            <div class="full-modal-content">
                <span id="confirm-action-message" style="font-size:13px"></span>
            </div>
            <div class="full-modal-footer">
                <button type="button" class="w-text" id="btn-confirm-action-cancel">Annulla</button>
                <button type="button" class="w-text active" id="btn-confirm-action-confirm">
                    <i data-lucide="check" class="lucide"></i>
                    <span id="confirm-action-confirm-text">Conferma</span>
                </button>
            </div>
        </div>
    </div>

    <!-- modali per menu -------------------------------------------------------------------------------------------------------- -->

    <div id="modal-menu" class="full-modal">
        <div id="side-menu-drawer">
            <div class="side-menu-header">
                <div class="side-menu-user">
                    <img class="profile-pic" data-profile-pic alt="">
                    <div class="side-menu-user-info">
                        <h2 id="saluto-menu">Ciao <span class="italic" data-get-info="username-name"></span>, al momento
                            l'auletta <span class="available italic" data-get-info="auletta-state"></span></h2>
                        <span class="side-menu-role disabled" data-get-info="username-role"></span>
                    </div>
                </div>
                <button type="button" class="no-style side-menu-close" aria-label="Chiudi menu"
                    data-modal="close-modal">
                    <i data-lucide="x" class="lucide"></i>
                </button>
            </div>

            <nav class="side-menu-nav">
                <span class="side-menu-section-label">Navigazione</span>
                <button type="button" class="side-menu-btn active" data-main-view="calendar" data-modal="close-modal">
                    <i data-lucide="calendar-days" class="lucide"></i>
                    <span>Calendario</span>
                </button>
                <button type="button" class="side-menu-btn" data-main-view="bookings" data-modal="close-modal">
                    <i data-lucide="calendar-clock" class="lucide"></i>
                    <span>Le mie prenotazioni</span>
                </button>
                <button type="button" class="side-menu-btn" data-main-view="account" data-modal="close-modal">
                    <i data-lucide="settings" class="lucide"></i>
                    <span>Impostazioni</span>
                </button>

                <div class="divider"></div>
                <span class="side-menu-section-label">Altre informazioni</span>

                <button type="button" class="side-menu-btn" data-modal="le-mie-prenotazioni">
                    <i data-lucide="bar-chart-2" class="lucide"></i>
                    <span>Riepilogo prenotazioni e limiti</span>
                </button>
                <button type="button" class="side-menu-btn" data-modal="notifiche">
                    <i data-lucide="inbox" class="lucide"></i>
                    <span>Notifiche</span>
                </button>
                <button type="button" class="side-menu-btn"
                    onclick="Livewire.dispatch('apriModalFeedback'); document.querySelector('[data-modal=\'close-modal\']')?.click();">
                    <i data-lucide="megaphone" class="lucide"></i>
                    <span>Lascia un feedback</span>
                </button>

                <div class="divider"></div>
                <span class="side-menu-section-label">Serve aiuto?</span>

                <button type="button" class="side-menu-btn" onclick="location.href='/guida.html'">
                    <i data-lucide="help-circle" class="lucide"></i>
                    <span>Visita la guida</span>
                </button>

                <div class="divider"></div>

                <button type="button" class="side-menu-btn side-menu-btn--danger" onclick="ldrLogout()">
                    <i data-lucide="log-out" class="lucide"></i>
                    <span>Esci</span>
                </button>
            </nav>
        </div>
    </div>



    <!-- scripts -->



    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>

    @livewireScripts

    @vite(['resources/js/app.js', 'resources/js/modal.js'])
    <livewire:modal-feedback />

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('toast', (event) => {
                window.showToast(event.tipo, event.messaggio, 'circle-check');
            });
        });
    </script>

</body>

</html>