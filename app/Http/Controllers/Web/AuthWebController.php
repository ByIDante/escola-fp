<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Services\Api\AuthApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthWebController extends Controller
{
    public function __construct(
        private readonly AuthApiService $authApiService,
    ) {
    }

    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('PRE-LOGIN', [
            'session_id' => $request->session()->getId(),
            'auth_check' => Auth::check(),
            'user' => Auth::user(),
        ]);

        try {
            $response = $this->authApiService->loginUser($credentials);

            // Buscar el usuario por email y autenticarlo en el guard web
            $user = \App\Models\User::where('email', $credentials['email'])->first();
            if ($user) {
                Auth::guard('web')->login($user);
                $request->session()->regenerate();
                \Log::info('POST-LOGIN', [
                    'session_id' => $request->session()->getId(),
                    'auth_check' => Auth::check(),
                    'user' => Auth::user(),
                ]);
            }

            // Opcional: guardar token si se desea usar en el frontend
            session(['api_token' => $response->token]);

            return redirect()->intended('dashboard');
        } catch (ApiException $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Muestra el formulario de registro.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Procesa el registro de usuario.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            // Cambiar validación para aceptar valores en mayúsculas
            'role' => 'required|in:STUDENT,TEACHER',
            'student_data.first_name' => 'nullable|string|max:255',
            'student_data.last_name' => 'nullable|string|max:255',
            'teacher_data.first_name' => 'nullable|string|max:255',
            'teacher_data.last_name' => 'nullable|string|max:255',
        ]);

        try {
            $this->authApiService->registerUser($validated);

            return redirect()->route('login')->with('status', 'Usuario registrado correctamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['register' => $e->getMessage()]);
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        try {
            $this->authApiService->logoutUser();
        } catch (\Throwable) {
            // Si falla, seguimos con logout local
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
