<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Feedback::class);

        return Feedback::with('utente:id_utente,nome,cognome,numero_tessera')
            ->latest('created_at')
            ->get();
    }

    public function store(Request $request)
    {
        $this->authorize('create', Feedback::class);

        $validated = $request->validate([
            'categoria' => ['required', 'in:bug,suggerimento,domanda,altro'],
            'contenuto' => ['required', 'string', 'max:2000'],
        ]);

        $feedback = Feedback::create([
            ...$validated,
            'id_utente' => Auth::user()->utente->id_utente,
        ]);

        return response()->json($feedback, 201);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $this->authorize('update', $feedback);

        $validated = $request->validate([
            'stato' => ['required', 'in:non_gestito,in_lavorazione,gestito'],
        ]);

        $feedback->update($validated);

        return response()->json($feedback);
    }

    public function destroy(Feedback $feedback)
    {
        $this->authorize('delete', $feedback);

        $feedback->delete();

        return response()->noContent();
    }
}