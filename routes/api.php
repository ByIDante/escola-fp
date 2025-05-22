<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\EvaluationApiController;
use App\Http\Controllers\Api\ModuleApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\TeacherApiController;
use App\Http\Controllers\Api\UnitApiController;
use Illuminate\Support\Facades\Route;

// Rutas públicas - accesibles sin autenticación
Route::post('login', [AuthApiController::class, 'login']);
Route::post('register', [AuthApiController::class, 'register'])->name('register');

// Ruta de verificación de usuario autenticado
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

// Rutas protegidas - requieren autenticación
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthApiController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileApiController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileApiController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileApiController::class, 'destroy'])->name('profile.destroy');

    // Student
    Route::get('/students', [StudentApiController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentApiController::class, 'store'])->name('students.store');
    Route::get('/students/{studentId}', [StudentApiController::class, 'show'])->name('students.show');
    Route::patch('/students/{studentId}', [StudentApiController::class, 'update'])->name('students.update');
    Route::delete('/students/{studentId}', [StudentApiController::class, 'destroy'])->name('students.destroy');

    // Teacher
    Route::get('/teachers', [TeacherApiController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherApiController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacherId}', [TeacherApiController::class, 'show'])->name('teachers.show');
    Route::patch('/teachers/{teacherId}', [TeacherApiController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacherId}', [TeacherApiController::class, 'destroy'])->name('teachers.destroy');

    // Evaluation
    Route::get('/evaluations', [EvaluationApiController::class, 'index'])->name('evaluations.index');
    Route::post('/evaluations', [EvaluationApiController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{evaluationId}', [EvaluationApiController::class, 'show'])->name('evaluations.show');
    Route::patch('/evaluations/{evaluationId}', [EvaluationApiController::class, 'update'])->name('evaluations.update');
    Route::delete('/evaluations/{evaluationId}', [EvaluationApiController::class, 'destroy'])->name('evaluations.destroy');
    Route::get('/students/{studentId}/evaluations', [EvaluationApiController::class, 'getStudentEvaluations'])->name('students.evaluations');
    Route::get('/teachers/{teacherId}/evaluations', [EvaluationApiController::class, 'getTeacherEvaluations'])->name('teachers.evaluations');

    // Module
    Route::get('/modules', [ModuleApiController::class, 'index'])->name('modules.index');
    Route::post('/modules', [ModuleApiController::class, 'store'])->name('modules.store');
    Route::get('/modules/{moduleId}', [ModuleApiController::class, 'show'])->name('modules.show');
    Route::patch('/modules/{moduleId}', [ModuleApiController::class, 'update'])->name('modules.update');
    Route::delete('/modules/{moduleId}', [ModuleApiController::class, 'destroy'])->name('modules.destroy');
    Route::get('/modules/{moduleId}/units', [ModuleApiController::class, 'getModuleUnits'])->name('modules.units');

    // Unit
    Route::get('/units', [UnitApiController::class, 'index'])->name('units.index');
    Route::post('/units', [UnitApiController::class, 'store'])->name('units.store');
    Route::get('/units/{unitId}', [UnitApiController::class, 'show'])->name('units.show');
    Route::patch('/units/{unitId}', [UnitApiController::class, 'update'])->name('units.update');
    Route::delete('/units/{unitId}', [UnitApiController::class, 'destroy'])->name('units.destroy');
    Route::get('/units/{unitId}/evaluations', [UnitApiController::class, 'getUnitEvaluations'])->name('units.evaluations');
});
