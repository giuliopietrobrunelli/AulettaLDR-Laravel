<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfiloController extends Controller
{
    public function update(Request $request)
    {
        $utente = Auth::user()->utente;

        $validated = Validator::make($request->all(), [
            'vista_predefinita' => ['sometimes', 'in:month,week'],
        ])->validate();

        $utente->update($validated);

        return response()->json($utente);
    }

    public function uploadFoto(Request $request)
    {
        $utente = Auth::user()->utente;

        $validated = Validator::make($request->all(), [
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ])->validate();

        // rimuove eventuale foto precedente prima di salvare la nuova
        if ($utente->foto_profilo) {
            Storage::disk('public')->delete($this->pathFromUrl($utente->foto_profilo));
        }

        $path = $request->file('foto')->store("profili/{$utente->id_utente}", 'public');

        $utente->update(['foto_profilo' => Storage::url($path)]);

        return response()->json($utente);
    }

    public function destroyFoto()
    {
        $utente = Auth::user()->utente;

        if ($utente->foto_profilo) {
            Storage::disk('public')->delete($this->pathFromUrl($utente->foto_profilo));
            $utente->update(['foto_profilo' => null]);
        }

        return response()->json($utente);
    }

    private function pathFromUrl(string $url): string
    {
        // Storage::url() antepone '/storage/', da togliere per risalire al path reale su disco
        return ltrim(str_replace('/storage/', '', $url), '/');
    }
}