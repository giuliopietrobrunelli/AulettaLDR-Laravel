<?php

namespace App\Http\Controllers;

use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UtenteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Utente::class);

        $query = Utente::query();

        if ($request->boolean('solo_registrati')) {
            $query->where('registrato', true);
        }

        if ($request->boolean('escludi_me')) {
            $query->where('id_utente', '!=', Auth::user()->utente->id_utente);
        }

        return $query->orderBy('cognome')->get();
    }

    public function show(Utente $utente)
    {
        $this->authorize('view', $utente);

        return $utente;
    }

    public function update(Request $request, Utente $utente)
    {
        $this->authorize('update', $utente);

        $validated = $request->validate([
            'email' => ['sometimes', 'email', 'max:255', 'unique:Utente,email,' . $utente->id_utente . ',id_utente'],
            'numero_tessera' => ['sometimes', 'digits_between:1,10', 'unique:Utente,numero_tessera,' . $utente->id_utente . ',id_utente'],
            'telefono' => ['sometimes', 'nullable', 'string', 'digits_between:1,10'],
            'facolta_universitaria' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cauzione' => ['sometimes', 'boolean'],
            'trattamento_dati' => ['sometimes', 'boolean'],
        ]);

        $utente->update($validated);

        return response()->json($utente);
    }
}