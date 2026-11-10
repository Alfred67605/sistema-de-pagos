<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\MessageBag;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = $this->throttleKey($request);
        $maxAttempts = 5;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = "Demasiados intentos de acceso fallidos. Su cuenta ha sido temporalmente bloqueada por seguridad. Intente nuevamente en {$seconds} segundos.";

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'retry_after' => $seconds,
                    'attempts' => RateLimiter::attempts($throttleKey)
                ], 429)->header('Retry-After', (string)$seconds);
            }

            $errorBag = new ViewErrorBag();
            $errorBag->put('default', new MessageBag(['email' => [$message]]));
            session()->flash('errors', $errorBag);

            return response()->view('auth.login', [
                'errors' => $errorBag,
                'retryAfter' => $seconds,
                'throttleMessage' => $message
            ], 429)->header('Retry-After', (string)$seconds);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, $decaySeconds);
        $remaining = RateLimiter::remaining($throttleKey, $maxAttempts);

        $failMessage = $remaining > 0
            ? "Las credenciales proporcionadas no coinciden con nuestros registros. Le quedan {$remaining} " . ($remaining === 1 ? 'intento' : 'intentos') . " antes del bloqueo temporal."
            : "Las credenciales proporcionadas no coinciden con nuestros registros. Ha alcanzado el límite máximo de intentos y su acceso ha sido bloqueado por {$decaySeconds} segundos.";

        return back()->withErrors([
            'email' => $failMessage,
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Administrador registrado con éxito.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
