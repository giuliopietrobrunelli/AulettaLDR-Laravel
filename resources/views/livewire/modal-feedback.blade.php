<div id="modal-feedback-wrapper">
    @if ($aperto)
        <div id="modal-feedback" class="full-modal showing" aria-hidden="false">
            <div class="full-modal-body">
                <div class="modal-header">
                    <span class="modal-title">Lascia un feedback</span>
                </div>
                <div class="full-modal-content">
                    <div class="feedback-categorie horizontal-container">
                        <label class="feedback-categoria-option">
                            <input type="radio" wire:model="categoria" value="bug">
                            <span>Bug</span>
                        </label>
                        <label class="feedback-categoria-option">
                            <input type="radio" wire:model="categoria" value="suggerimento">
                            <span>Suggerimento</span>
                        </label>
                        <label class="feedback-categoria-option">
                            <input type="radio" wire:model="categoria" value="altro">
                            <span>Altro</span>
                        </label>
                    </div>
                    @error('categoria') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror

                    <textarea wire:model="contenuto" rows="5"
                        placeholder="Descrivi il problema o lasciaci un feedback..."></textarea>
                    @error('contenuto') <span style="color:red;font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div class="full-modal-footer">
                    <button type="button" class="w-text" wire:click="chiudi">Annulla</button>
                    <button type="button" class="w-text active" wire:click="invia">
                        <i data-lucide="megaphone"></i>
                        <span>Invia feedback</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @script
    <script>
        lucide.createIcons();
        Livewire.hook('morph.updated', ({ el }) => lucide.createIcons());
    </script>
    @endscript
</div>