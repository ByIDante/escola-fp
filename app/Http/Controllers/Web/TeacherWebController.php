<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Api\TeacherApiService;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeacherWebController extends Controller
{
    public function __construct(
        private readonly TeacherApiService $teacherApiService,
    ) {
    }

    /**
     * Muestra el listado de profesores paginados.
     */
    public function index(Request $request): View
    {
        $teachers = $this->teacherApiService->getAllTeachers(
            filters: [],
            perPage: $request->get('per_page', 10)
        );

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Muestra el formulario para crear o completar perfil de profesor.
     */
    public function create(): View
    {
        return view('teachers.create');
    }

    /**
     * Almacena un nuevo perfil de profesor o completa el actual.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->teacherApiService->updateOrCreateTeacher($request->all());
            return redirect()->route('teachers.index')->with('success', 'Perfil de profesor creado correctamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Muestra la información de un profesor específico.
     */
    public function show(string $teacherId): View
    {
        try {
            $teacher = $this->teacherApiService->getTeacher((int) $teacherId);
            return view('teachers.show', compact('teacher'));
        } catch (ApiException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Muestra el formulario para editar un profesor.
     */
    public function edit(string $teacherId): View
    {
        try {
            $teacher = $this->teacherApiService->getTeacher((int) $teacherId);
            return view('teachers.edit', compact('teacher'));
        } catch (ApiException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualiza la información de un profesor.
     */
    public function update(Request $request, string $teacherId): RedirectResponse
    {
        try {
            $this->teacherApiService->updateOrCreateTeacher($request->all(), (int) $teacherId);
            return redirect()->route('teachers.show', $teacherId)->with('success', 'Profesor actualizado correctamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Elimina un profesor.
     */
    public function destroy(string $teacherId): RedirectResponse
    {
        try {
            $this->teacherApiService->deleteTeacher((int) $teacherId);
            return redirect()->route('teachers.index')->with('success', 'Profesor eliminado correctamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
