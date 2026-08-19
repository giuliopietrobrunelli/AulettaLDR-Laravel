{{-- resources/views/livewire/admin/gestione-turni.blade.php --}}
<div class="form-block">
    <h2>Gestione turni</h2>

    <div class="form-block">
        @foreach ($turni as $turno)
            <div class="horizontal-container" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                <div>
                    <strong>{{ $turno->indice }}° turno</strong>
                    <span style="color: #777; margin-left: 8px;">
                        {{ \Illuminate\Support\Carbon::parse($turno->orario_inizio)->format('H:i') }}
                        –
                        {{ \Illuminate\Support\Carbon::parse($turno->orario_fine)->format('H:i') }}
                    </span>
                </div>

                <button
                    type="button"
                    wire:click="toggleAttivo({{ $turno->id_turno }})"
                    wire:loading.attr="disabled"
                    class="{{ $turno->attivo ? 'active' : '' }}"
                >
                    {{ $turno->attivo ? 'Attivo' : 'Disattivo' }}
                </button>
            </div>
        @endforeach
    </div>
</div>