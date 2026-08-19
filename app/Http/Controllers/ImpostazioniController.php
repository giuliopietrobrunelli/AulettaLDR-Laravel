<?php

namespace App\Http\Controllers;

use App\Models\Impostazioni;
use Illuminate\Http\Request;

class ImpostazioniController extends Controller
{
    public function show()
    {
        return response()->json([
            'limite_settimanale' => (int) Impostazioni::where('nome', 'limite_settimanale')->value('valore'),
            'settimane_anticipo' => (int) Impostazioni::where('nome', 'settimane_anticipo')->value('valore'),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('updateAny', Impostazioni::class);

        $validated = $request->validate([
            'limite_settimanale' => ['sometimes', 'integer', 'min:1'],
            'settimane_anticipo' => ['sometimes', 'integer', 'min:0'],
        ]);

        foreach ($validated as $nome => $valore) {
            Impostazioni::set($nome, $valore);
        }

        return $this->show();
    }
}