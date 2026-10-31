<?php

namespace App\Modules\SuperAdmin\Terminal\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\Terminal\Requests\ExecuteCommandRequest;
use App\Modules\SuperAdmin\Terminal\Services\TerminalService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TerminalController extends Controller
{
    public function __construct(
        protected TerminalService $terminalService
    ) {}

    public function index(): View
    {
        $systemInfo = $this->terminalService->getSystemInfo();

        return view('superadmin.terminal.index', compact('systemInfo'));
    }

    public function execute(ExecuteCommandRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $command = $validated['command'];
        $confirmed = (bool) ($validated['confirmed'] ?? false);
        $result = $this->terminalService->execute($command, $confirmed);

        return response()->json($result);
    }

    public function saveFile(\Illuminate\Http\Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
        ]);

        $result = $this->terminalService->saveFile($validated['file'], $validated['content'] ?? '');

        return response()->json($result);
    }
}
