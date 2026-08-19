<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TurnoController extends Controller
{
    public function index(Request $request)
    {
        $soloAttivi = $request->boolean('solo_attivi', true);

        $query = Turno::query();
        if ($soloAttivi) {
            $query->where('attivo', true);
        }

        return $query->orderBy('orario_inizio')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('create', Turno::class);

        $validated = Validator::make($request->all(), [
            'orario_inizio' => ['required', 'date_format:H:i:s'],
            'orario_fine' => ['required', 'date_format:H:i:s', 'after:orario_inizio'],
        ])->validate();

        $turno = Turno::create($validated);

        return response()->json($turno, 201);
    }

    public function update(Request $request, Turno $turno)
    {
        $this->authorize('update', $turno);

        $validated = Validator::make($request->all(), [
            'orario_inizio' => ['required', 'date_format:H:i:s'],
            'orario_fine' => ['required', 'date_format:H:i:s', 'after:orario_inizio'],
        ])->validate();

        $turno->update($validated);

        return response()->json($turno);
    }

    public function deactivate(Turno $turno)
    {
        $this->authorize('deactivate', $turno);

        $turno->update(['attivo' => ! $turno->attivo]);

        return response()->json($turno);
    }

    public function destroy(Turno $turno)
    {
        $this->authorize('delete', $turno);

        $turno->delete();

        return response()->noContent();
    }
}