<span class="{{ $stato }}">
    @switch($stato)
        @case('available')
            è libera
            @break
        @case('mine')
            è occupata da te
            @break
        @case('occupied')
            è occupata
            @break
        @case('unavailable')
            non è disponibile
            @break
    @endswitch
</span>