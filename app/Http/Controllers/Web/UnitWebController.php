<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Api\UnitApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Exceptions\ApiException;

class UnitWebController extends Controller
{
    public function __construct(
        private readonly UnitApiService $unitApiService,
    ) {
    }

    public function index(): View
    {
        $units = $this->unitApiService->getAllUnits(perPage: 50); // o el número que quieras mostrar
        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $this->unitApiService->createUnit($request->all());
            return redirect()->route('units.index')->with('success', 'Unidad creada correctamente');
        } catch (ApiException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function show(string $unitId): View
    {
        try {
            $unit = $this->unitApiService->getUnit((int) $unitId);
            return view('units.show', compact('unit'));
        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function edit(string $unitId): View
    {
        try {
            $unit = $this->unitApiService->getUnit((int) $unitId);
            return view('units.edit', compact('unit'));
        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function update(Request $request, string $unitId): RedirectResponse
    {
        try {
            $this->unitApiService->updateUnit($request->all(), (int) $unitId);
            return redirect()->route('units.show', $unitId)->with('success', 'Unidad actualizada correctamente');
        } catch (ApiException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(string $unitId): RedirectResponse
    {
        try {
            $this->unitApiService->deleteUnit((int) $unitId);
            return redirect()->route('units.index')->with('success', 'Unidad eliminada correctamente');
        } catch (ApiException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function unitEvaluations(string $unitId): View
    {
        try {
            $unit = $this->unitApiService->getUnit((int) $unitId);
            $evaluations = $this->unitApiService->getUnitEvaluations((int) $unitId, [], 50);
            return view('units.evaluations', compact('unit', 'evaluations'));
        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }
}
