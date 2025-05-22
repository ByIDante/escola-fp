<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Services\Api\ModuleApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ModuleWebController extends Controller
{
    public function __construct(
        private readonly ModuleApiService $moduleService,
    ) {
    }

    public function index(): View
    {
        $modules = $this->moduleService->getAllModules();
        return view('modules.index', compact('modules'));
    }

    public function create(): View
    {
        return view('modules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $this->moduleService->createModule($request->all());
            return redirect()->route('modules.index')->with('success', 'Módulo creado correctamente');
        } catch (ApiException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(int $moduleId): View
    {
        try {
            $module = $this->moduleService->getModule($moduleId);
            return view('modules.show', compact('module'));
        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function edit(int $moduleId): View
    {
        try {
            $module = $this->moduleService->getModule($moduleId);
            return view('modules.edit', compact('module'));
        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function update(Request $request, int $moduleId): RedirectResponse
    {
        try {
            $this->moduleService->updateModule($request->all(), $moduleId);
            return redirect()->route('modules.show', $moduleId)->with('success', 'Módulo actualizado correctamente');
        } catch (ApiException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(int $moduleId): RedirectResponse
    {
        try {
            $this->moduleService->deleteModule($moduleId);
            return redirect()->route('modules.index')->with('success', 'Módulo eliminado correctamente');
        } catch (ApiException $e) {
            return redirect()->route('modules.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function moduleUnits(int $moduleId): View
    {
        try {
            $module = $this->moduleService->getModule($moduleId);
            $units = $this->moduleService->getModuleUnits($moduleId);
            return view('modules.units', compact('module', 'units'));
        } catch (ApiException $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }
}
