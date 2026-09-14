<div id="calendario-mese-wrapper">
    <div id="calendario-mese-wrapper">
        <div id="calendar-header">
            <div id="indicatore-data-prenota-turni-container" class="horizontal-container">
                <h1 id="current-date">{{ ucfirst($titoloMese) }}</h1>
            </div>

            <div class="horizontal-container" data-view="month">
                <button type="button" wire:click="meseIndietro" class="mini" aria-label="Vai al mese precedente"
                    @disabled(!$puoIndietro)>
                    <i data-lucide="chevron-left"></i>
                </button>
                <button type="button" wire:click="meseOggi" class="mini" aria-label="Vai al giorno attuale">
                    <span>Oggi</span>
                </button>
                <button type="button" wire:click="meseAvanti" class="mini" aria-label="Vai al mese successivo"
                    @disabled(!$puoAvanti)>
                    <i data-lucide="chevron-right"></i>
                </button>

                <div class="vertical-separator"></div>

                <button type="button" class="w-text" data-modal="prenota">
                    <i data-lucide="plus"></i>
                    <span>Prenota</span>
                </button>
            </div>
        </div>

        <div id="calendar-container">
            <div id="calendar-columns">
                <h3>lun</h3>
                <h3>mar</h3>
                <h3>mer</h3>
                <h3>gio</h3>
                <h3>ven</h3>
                <h3>sab</h3>
                <h3>dom</h3>
            </div>

            <div id="calendar-rows">
                @foreach ($giorni->chunk(7) as $settimana)
                    <div class="calendar-row">
                        @foreach ($settimana as $giorno)
                            <div wire:key="{{ $giorno['data']->format('Y-m-d') }}" class="calendar-day
                                                @if(!$giorno['nel_mese']) inactive @endif
                                                @if($giorno['passato']) past @endif
                                                @if($giorno['locked']) locked @endif
                                                @if($giorno['oggi']) current-day @endif" @if($giorno['nel_mese'] && !$giorno['passato'] && !$giorno['locked'])
                                                wire:click="vaiAlGiorno('{{ $giorno['data']->format('Y-m-d') }}')" @endif>
                                <span>{{ $giorno['data']->day }}</span>

                                @if ($giorno['prenotazioni']->isNotEmpty())
                                    <div class="booked-day-recap shorted">
                                        @foreach ($giorno['prenotazioni'] as $p)
                                            <div class="dot"></div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        @script
        <script>
            lucide.createIcons();

            Livewire.hook('morph.updated', ({ el }) => {
                lucide.createIcons();
            });
        </script>
        @endscript
    </div>