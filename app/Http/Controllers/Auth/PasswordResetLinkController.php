<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // En este entorno todavía no hay un servidor de correo real conectado
        // (MAIL_MAILER=log en .env): el correo "se envía" pero solo queda
        // escrito en storage/logs/laravel.log. Para poder probar el flujo
        // completo de recuperación sin salir del navegador, en local
        // generamos el mismo link y lo mostramos directo en la pantalla.
        // Esto solo ocurre con app()->environment('local'), así que no
        // representa ningún riesgo una vez haya un mailer real configurado.
        if ($status == Password::RESET_LINK_SENT && app()->environment('local')) {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                $token = Password::broker()->createToken($user);

                return back()
                    ->with('status', __($status))
                    ->with('dev_reset_url', route('password.reset', [
                        'token' => $token,
                        'email' => $user->email,
                    ]));
            }
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
