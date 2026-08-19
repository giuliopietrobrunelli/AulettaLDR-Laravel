<?php

namespace App\Http\Controllers;

use App\Actions\RichiesteCessione\AccettaRichiestaCessione;
use App\Actions\RichiesteCessione\CediPrenotazione;
use App\Models\Prenotazione;
use App\Models\RichiestaCessione;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RichiestaCessioneController extends Controller
{
    // richieste in arrivo per l'utente loggato ("casella postale")
    public function index()
    {
        $utente = Auth::user()->utente;

        return RichiestaCessione::with([
                'prenotazione.turno',
                'mittente:id_utente,nome,cognome,foto_profilo',
            ])
            ->where('id_destinatario', $utente->id_utente)
            ->where('stato', 'in_attesa')
            ->latest('created_at')
            ->get();
    }

    public function store(Request $request, Prenotazione $prenotazione, CediPrenotazione $cediPrenotazione)
    {
        $validated = Validator::make($request->all(), [
            'id_destinatario' => ['required', 'integer', 'exists:Utente,id_utente'],
        ])->validate();

        $destinatario = Utente::findOrFail($validated['id_destinatario']);

        $this->authorize('create', [RichiestaCessione::class, $prenotazione, $destinatario]);

        $richiesta = $cediPrenotazione->handle($prenotazione, $destinatario->id_utente);

        return response()->json($richiesta, 201);
    }

    public function accept(RichiestaCessione $richiesta, AccettaRichiestaCessione $accettaRichiesta)
    {
        $this->authorize('accept', $richiesta);

        $accettaRichiesta->handle($richiesta);

        return response()->json(['stato' => 'accettata']);
    }

    public function reject(RichiestaCessione $richiesta)
    {
        $this->authorize('reject', $richiesta);

        $richiesta->update(['stato' => 'rifiutata']);

        return response()->json(['stato' => 'rifiutata']);
    }
}