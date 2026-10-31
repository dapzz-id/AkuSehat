<?php

namespace App\Modules\SuperAdmin\SystemConfig\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\SystemConfig\Services\SoftwareAppService;
use App\Modules\SuperAdmin\SystemConfig\Requests\FilterPatchNoteRequest;

class PatchNoteController extends Controller
{
    public function __construct(
        protected SoftwareAppService $softwareAppService
    ) {}

    public function index(FilterPatchNoteRequest $request)
    {
        $platform = $request->validated('platform');
        $data = $this->softwareAppService->getPatchNotesData($platform);

        return view('patch-notes.index', $data);
    }
}
