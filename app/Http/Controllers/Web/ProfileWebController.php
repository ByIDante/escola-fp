<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Api\ProfileApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileWebController extends Controller
{
    public function __construct(
        private readonly ProfileApiService $profileApiService,
    ) {
    }

    /**
     * Muestra el perfil del usuario autenticado.
     */
    public function show(): View
    {
        try {
            $userId = Auth::id();
            $profile = $this->profileApiService->getProfile($userId);

            return view('profile.show', compact('profile'));

        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar el perfil.
     */
    public function edit(): View
    {
        try {
            $userId = Auth::id();
            $profile = $this->profileApiService->getProfile($userId);

            return view('profile.edit', compact('profile'));

        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Actualiza el perfil del usuario.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'current_password' => ['required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->profileApiService->updateProfile($validated);

            return redirect()
                ->route('profile.show')
                ->with('success', 'Perfil actualizado correctamente');

        } catch (ApiException $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Elimina la cuenta del usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        try {
            $this->profileApiService->deleteProfile($request->input('password'));

            // Cierra sesión después de eliminar
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')
                ->with('success', 'Tu cuenta ha sido eliminada correctamente');

        } catch (ApiException $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
