<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\GenerateVerificationCode;
use App\Http\Controllers\Controller;
use App\Models\Utente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\PasswordValidationRules;

class RegistrationController extends Controller
{   
    use PasswordValidationRules;
    public function store(Request $request, GenerateVerificationCode $generateCode)
    {
        $isAlternative = $request->input('registration_type') === 'alternative';

        $utente = $isAlternative
            ? $this->findByNomeCognome($request)
            : $this->findByTessera($request);

        $generateCode->handle($utente);

        // stato "registrazione in corso" salvato in sessione, MAI nell'URL
        $request->session()->put('pending_registration.id_utente', $utente->id_utente);
        $request->session()->put('pending_registration.email_masked', $this->maskEmail($utente->email));

        return redirect()->route('register.verify');
    }

    private function findByTessera(Request $request): Utente
    {
        $validated = Validator::make($request->all(), [
            'n_tessera' => ['required', 'digits_between:1,10'],
        ])->validate();

        $utente = Utente::where('numero_tessera', $validated['n_tessera'])
            ->where('registrato', false)
            ->first();

        if (! $utente) {
            throw ValidationException::withMessages([
                'n_tessera' => 'Numero tessera non trovato oppure già registrato.',
            ]);
        }

        return $utente;
    }

    private function findByNomeCognome(Request $request): Utente
    {
        $validated = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'max:255'],
            'cognome' => ['required', 'string', 'max:255'],
        ])->validate();

        $candidati = Utente::where('nome', $validated['nome'])
            ->where('cognome', $validated['cognome'])
            ->where('registrato', false)
            ->get();

        $candidatoregistrato = Utente::where('nome', $validated['nome'])
            ->where('cognome', $validated['cognome'])
            ->where('registrato', true)
            ->get();

        if($candidatoregistrato->isNotEmpty()){
            throw ValidationException::withMessages([
                'nome' => 'Un account con queste credenziali risulta già registrato',
            ]);
        }

        if ($candidati->isEmpty()) {
            throw ValidationException::withMessages([
                'nome' => 'Nessun socio trovato con questi dati. Contatta il direttivo.',
            ]);
        }

        if ($candidati->count() > 1) {
            // caso omonimia: rimando l'utente a registrarsi tramite tessera
            throw ValidationException::withMessages([
                'nome' => 'Trovati più utenti con lo stesso nome e cognome: registrati usando il numero tessera.',
            ])->redirectTo(route('register'));
        }

        return $candidati->first();
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $visible = mb_substr($local, 0, 2);

        return $visible.str_repeat('*', max(mb_strlen($local) - 2, 1)).'@'.$domain;
    }

    public function showVerifyForm(Request $request)
    {
        if (! $request->session()->has('pending_registration.id_utente')) {
            return redirect()->route('register');
        }

        return view('auth.register-verify', [
            'emailMasked' => $request->session()->get('pending_registration.email_masked'),
        ]);
    }

    public function verify(Request $request)
    {
        $idUtente = $request->session()->get('pending_registration.id_utente');

        if (! $idUtente) {
            return redirect()->route('register');
        }

        $validated = Validator::make($request->all(), [
            'code' => ['required', 'digits:6'],
            'password' => $this->passwordRules(),
        ])->validate();

        $utente = Utente::findOrFail($idUtente);

        $verificationCode = $utente->verificationCodes()
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (! $verificationCode) {
            throw ValidationException::withMessages([
                'code' => 'Nessun codice attivo. Torna indietro e registrati di nuovo.',
            ]);
        }

        if ($verificationCode->isExpired()) {
            throw ValidationException::withMessages([
                'code' => 'Il codice è scaduto. Torna indietro e registrati di nuovo.',
            ]);
        }

        if ($verificationCode->attemptsExceeded()) {
            throw ValidationException::withMessages([
                'code' => 'Troppi tentativi errati. Torna indietro e registrati di nuovo.',
            ]);
        }

        if (! Hash::check($validated['code'], $verificationCode->code)) {
            $verificationCode->increment('attempts');

            $rimasti = 5 - $verificationCode->attempts;

            throw ValidationException::withMessages([
                'code' => "Codice errato. Tentativi rimasti: {$rimasti}.",
            ]);
        }

        $user = DB::transaction(function () use ($validated, $utente, $verificationCode) {
            $user = User::create([
                'email' => $utente->email,
                'password' => Hash::make($validated['password']),
            ]);

            $utente->update([
                'user_id' => $user->id,
                'registrato' => true,
            ]);

            $verificationCode->update(['consumed_at' => now()]);

            return $user;
        });

        $request->session()->forget('pending_registration');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('app'));
    }
}