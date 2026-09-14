<?php

namespace App\Livewire\Admin;

use App\Models\Feedback;
use Livewire\Component;

class GestioneFeedback extends Component
{
    public ?int $feedbackApertoId = null;
    public string $statoModal = 'non_gestito';

    public function apriDettaglio(int $idFeedback)
    {
        $feedback = Feedback::findOrFail($idFeedback);
        $this->feedbackApertoId = $feedback->id_feedback;
        $this->statoModal = $feedback->stato;
        $this->dispatch('modal-aperto');
    }

    public function chiudiDettaglio()
    {
        $this->feedbackApertoId = null;
    }

    public function salvaStato()
    {
        $feedback = Feedback::findOrFail($this->feedbackApertoId);
        $this->authorize('update', $feedback);

        $feedback->update(['stato' => $this->statoModal]);

        $this->chiudiDettaglio();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Stato feedback aggiornato.');
    }

    public function elimina()
    {
        $feedback = Feedback::findOrFail($this->feedbackApertoId);
        $this->authorize('delete', $feedback);

        $feedback->delete();
        $this->chiudiDettaglio();
        $this->dispatch('toast', tipo: 'success', messaggio: 'Feedback eliminato.');
    }

    public function render()
    {
        $this->authorize('viewAny', Feedback::class);

        $feedback = Feedback::with('utente')
            ->orderByDesc('created_at')
            ->get();

        $feedbackAperto = $this->feedbackApertoId
            ? Feedback::with('utente')->find($this->feedbackApertoId)
            : null;

        return view('livewire.admin.gestione-feedback', [
            'feedback' => $feedback,
            'feedbackAperto' => $feedbackAperto,
        ]);
    }
}