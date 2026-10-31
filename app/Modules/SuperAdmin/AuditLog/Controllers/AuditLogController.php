<?php

namespace App\Modules\SuperAdmin\AuditLog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\AuditLog\Services\AuditLogService;
use App\Modules\SuperAdmin\AuditLog\Requests\FilterAuditLogRequest;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(FilterAuditLogRequest $request)
    {
        $data = $this->auditLogService->getDashboardData($request->validated());

        return view('superadmin.logs.index', $data);
    }
}
