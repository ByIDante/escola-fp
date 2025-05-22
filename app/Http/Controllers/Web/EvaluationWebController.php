<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Api\EvaluationApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Exceptions\ApiException;

class EvaluationWebController extends Controller
{
    public function __construct(
        private readonly EvaluationApiService $evaluationService,
    ) {
    }

    public function index(): View
    {
        $evaluations = $this->evaluationService->getAllEvaluations();
        return view('evaluations.index', compact('evaluations'));
    }

    public function create(): View
    {
        return view('evaluations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $this->evaluationService->updateOrCreateEvaluation($request->all());
            return redirect()->route('evaluations.index')->with('success', 'Evaluación creada correctamente');
        } catch (ApiException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function show(string $evaluationId): View
    {
        try {
            $evaluation = $this->evaluationService->getEvaluation((int) $evaluationId);
            return view('evaluations.show', compact('evaluation'));
        } catch (ApiException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function edit(string $evaluationId): View
    {
        try {
            $evaluation = $this->evaluationService->getEvaluation((int) $evaluationId);
            return view('evaluations.edit', compact('evaluation'));
        } catch (ApiException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(Request $request, string $evaluationId): RedirectResponse
    {
        try {
            $this->evaluationService->updateOrCreateEvaluation($request->all(), (int) $evaluationId);
            return redirect()->route('evaluations.show', $evaluationId)->with('success', 'Evaluación actualizada correctamente');
        } catch (ApiException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $evaluationId): RedirectResponse
    {
        try {
            $this->evaluationService->deleteEvaluation((int) $evaluationId);
            return redirect()->route('evaluations.index')->with('success', 'Evaluación eliminada correctamente');
        } catch (ApiException $e) {
            return redirect()->route('evaluations.index')->withErrors($e->getMessage());
        }
    }

    public function studentEvaluations(string $studentId): View
    {
        $evaluations = $this->evaluationService->getStudentEvaluations((int) $studentId);
        return view('evaluations.student', compact('evaluations', 'studentId'));
    }

    public function teacherEvaluations(string $teacherId): View
    {
        $evaluations = $this->evaluationService->getTeacherEvaluations((int) $teacherId);
        return view('evaluations.teacher', compact('evaluations', 'teacherId'));
    }
}
