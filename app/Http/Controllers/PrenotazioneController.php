<?php

namespace App\Http\Controllers;

use App\Actions\Prenotazioni\CreatePrenotazione;
use App\Models\Prenotazione;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Policies\Concerns\AutorizzaAdmin;
use Illuminate\Validation\ValidationException;

class PrenotazioneController extends Controller
{
    // prenotazioni in un intervallo di date, con turno e utente collegati (per il calendario)
    public function index(Request $request)
{
    $validated = Validator::make($request->all(), [
        'start' => ['required', 'date'],
        'end' => ['required', 'date', 'after_or_equal:start'],
    ])->validate();

    return Prenotazione::query()
        ->join('Turno', 'Turno.id_turno', '=', 'Prenotazione.id_turno')
        ->with(['turno', 'utente:id_utente,nome,cognome,foto_profilo'])
        ->where('Turno.attivo', true)
        ->whereBetween('Prenotazione.data_prenotazione', [$validated['start'], $validated['end']])
        ->select('Prenotazione.*')
        ->orderBy('Prenotazione.data_prenotazione')
        ->orderBy('Turno.orario_inizio')
        ->get();
}

    // prenotazioni dell'utente loggato da una certa data in poi
    public function mine(Request $request)
    {
        $utente = Auth::user()->utente;

        $from = $request->input('from', now()->toDateString());

        return Prenotazione::with('turno')
            ->where('id_utente', $utente->id_utente)
            ->whereHas('turno', fn($q) => $q->where('attivo', true))
            ->where('data_prenotazione', '>=', $from)
            ->orderBy('data_prenotazione')->orderBy(
        Turno::select('orario_inizio')
            ->whereColumn('Turno.id_turno', 'Prenotazione.id_turno')
    )
            ->get();
    }

    use AutorizzaAdmin;

    public function store(Request $request, CreatePrenotazione $createPrenotazione)
{
    $isAdmin = $this->isAdmin($request->user());

    $rules = [
        'id_turno' => ['required', 'integer', 'exists:Turno,id_turno'],
        'data_prenotazione' => ['required', 'date'],
    ];

    if ($isAdmin) {
        $rules['stato'] = ['nullable', 'in:non_confermata,confermata,riservata'];
        $rules['id_utente'] = ['nullable', 'integer', 'exists:Utente,id_utente'];
        $rules['forza'] = ['nullable', 'boolean'];
    }

    $validated = Validator::make($request->all(), $rules)->validate();

    $this->authorize('create', [Prenotazione::class, $validated['data_prenotazione']]);

    $idUtenteDestinatario = $isAdmin && ! empty($validated['id_utente'])
        ? $validated['id_utente']
        : $request->user()->utente->id_utente;

    $prenotazione = $createPrenotazione->handle(
        $idUtenteDestinatario,
        $validated['id_turno'],
        $validated['data_prenotazione'],
        $isAdmin ? ($validated['stato'] ?? null) : null,
        $isAdmin && ($validated['forza'] ?? false),
    );

    return response()->json($prenotazione->load(['turno', 'utente']), 201);
}

    public function confirm(Prenotazione $prenotazione)
    {
        $this->authorize('confirm', $prenotazione);

        $prenotazione->update([
            'stato' => 'confermata',
            'data_conferma' => now(),
        ]);

        return response()->json($prenotazione);
    }

    public function update(Request $request, Prenotazione $prenotazione)
{
    $this->authorize('update', $prenotazione);

    $validated = Validator::make($request->all(), [
        'id_utente' => ['sometimes', 'integer', 'exists:Utente,id_utente'],
        'stato' => ['sometimes', 'in:non_confermata,confermata,riservata'],
    ])->validate();

    $prenotazione->update($validated);

    return response()->json($prenotazione->load(['turno', 'utente']));
}

    public function destroy(Prenotazione $prenotazione)
    {
        $this->authorize('delete', $prenotazione);

        $prenotazione->delete();

        return response()->noContent();
    }
}