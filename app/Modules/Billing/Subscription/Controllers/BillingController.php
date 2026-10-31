<?php

namespace App\Modules\Billing\Subscription\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Modules\Billing\Subscription\Services\BillingService;
use App\Modules\Billing\Subscription\Requests\ProcessGooglePayRequest;
use App\Modules\Billing\Subscription\Requests\ProcessGooglePlayInAppRequest;
use App\Modules\Billing\Subscription\Requests\DownloadInvoiceRequest;

class BillingController extends Controller
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    public function index(): View|RedirectResponse
    {
        try {
            $data = $this->billingService->getBillingDashboardData(Auth::user());
            return view('admin.billing.index', $data);
        } catch (\Throwable $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    public function processGooglePay(ProcessGooglePayRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->billingService->processGooglePay(Auth::user(), $request->validated());

        if (isset($result['response']['is_duplicate']) && $result['response']['is_duplicate']) {
            return redirect()->route('admin.billing.index')
                ->with('info', $result['response']['message']);
        }

        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function processGooglePlayInApp(ProcessGooglePlayInAppRequest $request): JsonResponse
    {
        $result = $this->billingService->processGooglePlayInApp(Auth::user(), $request->validated());
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function downloadInvoicePdf(DownloadInvoiceRequest $request, int|string $id): Response
    {
        $isPreview = $request->boolean('preview') || $request->boolean('stream');
        return $this->billingService->generateInvoicePdf($id, Auth::user(), $isPreview);
    }
}
