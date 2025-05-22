<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EvaluationWebController;
use App\Http\Controllers\Web\ModuleWebController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\StudentWebController;
use App\Http\Controllers\Web\TeacherWebController;
use App\Http\Controllers\Web\UnitWebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

// Página de inicio
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación SOLO personalizadas
Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::get('/register', [AuthWebController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthWebController::class, 'register']);

// Ruta de diagnóstico de sesión (NO requiere login)
Route::get('/session-test', function () {
    $count = Session::get('count', 0);
    Session::put('count', $count + 1);
    return "Session count: " . Session::get('count');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Logout personalizado
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileWebController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileWebController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileWebController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileWebController::class, 'destroy'])->name('profile.destroy');

    // Estudiantes
    Route::resource('students', StudentWebController::class);

    // Profesores
    Route::resource('teachers', TeacherWebController::class);

    // Evaluaciones
    Route::resource('evaluations', EvaluationWebController::class);
    Route::get('/students/{studentId}/evaluations', [EvaluationWebController::class, 'studentEvaluations'])->name('students.evaluations');
    Route::get('/teachers/{teacherId}/evaluations', [EvaluationWebController::class, 'teacherEvaluations'])->name('teachers.evaluations');

    // Módulos
    Route::resource('modules', ModuleWebController::class);
    Route::get('/modules/{moduleId}/units', [ModuleWebController::class, 'moduleUnits'])->name('modules.units');

    // Unidades
    Route::resource('units', UnitWebController::class);
    Route::get('/units/{unitId}/evaluations', [UnitWebController::class, 'unitEvaluations'])->name('units.evaluations');
});
