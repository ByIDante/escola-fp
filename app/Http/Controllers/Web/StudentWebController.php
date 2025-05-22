<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Api\StudentApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StudentWebController extends Controller
{
    /**
     * Constructor del controlador.
     */
    public function __construct(
        private readonly StudentApiService $studentService,
    ) {
    }

    /**
     * Muestra el listado de estudiantes.
     */
    public function index(): View
    {
        \Log::info('STUDENTS-INDEX', [
            'session_id' => session()->getId(),
            'auth_check' => \Auth::check(),
            'user' => \Auth::user(),
        ]);

        try {
            $students = $this->studentService->getAllStudents();
            dd($students);
        } catch (\Throwable $e) {
            $students = [];
        }
        return view('students.index', compact('students'));
    }

    /**
     * Muestra el formulario para crear un estudiante.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Almacena un nuevo estudiante.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->studentService->updateOrCreateStudent($request->all());
        return redirect()->route('students.index')->with('success', 'Estudiante creado correctamente');
    }

    /**
     * Muestra la información de un estudiante específico.
     */
    public function show(string $studentId): View
    {
        $student = $this->studentService->getStudent((int) $studentId);
        return view('students.show', compact('student'));
    }

    /**
     * Muestra el formulario para editar un estudiante.
     */
    public function edit(string $studentId): View
    {
        $student = $this->studentService->getStudent((int) $studentId);
        return view('students.edit', compact('student'));
    }

    /**
     * Actualiza la información de un estudiante.
     */
    public function update(Request $request, string $studentId): RedirectResponse
    {
        $this->studentService->updateOrCreateStudent($request->all(), (int) $studentId);
        return redirect()->route('students.show', $studentId)->with('success', 'Estudiante actualizado correctamente');
    }

    /**
     * Elimina un estudiante.
     */
    public function destroy(string $studentId): RedirectResponse
    {
        $this->studentService->deleteStudent((int) $studentId);
        return redirect()->route('students.index')->with('success', 'Estudiante eliminado correctamente');
    }
}
