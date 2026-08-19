{{-- resources/views/emails/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reimposta password — Auletta LDR</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f4f4f4; padding: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width: 480px; background-color: #ffffff; border-radius: 10px; overflow: hidden;">

                    {{-- Header con logo --}}
                    <tr>
                        <td align="center" style="background-color: #1a1a1a; padding: 25px 20px;">
                            <img src="{{ $message->embed(public_path('img/logo-prenotaldr-giallo.webp')) }}"
                                alt="Logo Auletta LDR" style="height: 48px;">
                        </td>
                    </tr>

                    {{-- Corpo --}}
                    <tr>
                        <td style="padding: 35px 30px;">
                            <h1 style="font-size: 20px; color: #1a1a1a; margin: 0 0 15px;">Ciao {{ $nome }},</h1>

                            <p style="font-size: 15px; color: #333333; line-height: 1.5; margin: 0 0 20px;">
                                hai richiesto di reimpostare la password del tuo account su
                                <strong>Auletta LDR</strong>. Clicca sul pulsante qui sotto per procedere:
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}"
                                    style="display: inline-block; font-size: 15px; font-weight: bold; color: #1a1a1a; background-color: #ffd54f; text-decoration: none; padding: 14px 30px; border-radius: 8px;">
                                    Reimposta password
                                </a>
                            </div>

                            <p
                                style="font-size: 14px; color: #65c45c; font-weight: bold; text-align: center; margin: 0 0 20px;">
                                Il link è valido per 60 minuti
                            </p>

                            <p style="font-size: 13px; color: #777777; line-height: 1.5; margin: 0 0 15px;">
                                Se il pulsante non funziona clicca <a href="{{ $url }}"
                                    style="text-decoration-underline">
                                    qui
                                </a>
                            </p>
                            

                            <p style="font-size: 13px; color: #777777; line-height: 1.5; margin: 0;">
                                Se non hai richiesto tu il reset della password, ignora pure questa email: nessuna
                                azione verrà eseguita sul tuo account.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f4f4f4; padding: 18px 30px; text-align: center;">
                            <p style="font-size: 12px; color: #999999; margin: 0;">
                                © {{ date('Y') }} {{ config('app.name') }} — Tutti i diritti riservati
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>