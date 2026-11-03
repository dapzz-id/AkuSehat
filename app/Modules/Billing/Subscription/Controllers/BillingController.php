<?php

namespace App\Modules\Billing\Subscription\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Modules\Billing\Subscription\Services\BillingService;
use App\Modules\Billing\Subscription\Requests\ProcessGooglePayRequest;
use App\Modules\Billing\Subscription\Requests\ProcessManualTransferRequest;
use App\Modules\Billing\Subscription\Requests\ProcessMidtransSnapRequest;
use App\Modules\Billing\Subscription\Requests\MidtransNotificationRequest;
use App\Modules\Billing\Subscription\Requests\UpdatePaymentMethodSettingRequest;
use App\Modules\Billing\Subscription\Requests\ProcessGooglePlayInAppRequest;
use App\Modules\Billing\Subscription\Requests\DownloadInvoiceRequest;
use App\Modules\Billing\Subscription\Requests\SuperAdminBillingFilterRequest;
use App\Modules\Billing\Subscription\Requests\RejectManualTransferRequest;
use App\Modules\Billing\Subscription\Requests\SwitchPaymentMethodRequest;
use App\Modules\Billing\Subscription\Requests\CancelBillingTransactionRequest;

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

    public function processManualTransfer(ProcessManualTransferRequest $request): JsonResponse
    {
        $result = $this->billingService->processManualTransfer(Auth::user(), $request->validated());
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function createMidtransSnap(ProcessMidtransSnapRequest $request): JsonResponse
    {
        $result = $this->billingService->processMidtransSnap(Auth::user(), $request->validated());
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function handleMidtransNotification(MidtransNotificationRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $result = $this->billingService->handleMidtransNotification($payload);
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function checkMidtransStatus(string $orderId): JsonResponse
    {
        $result = $this->billingService->checkMidtransStatus(Auth::user(), $orderId);
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function superAdminIndex(SuperAdminBillingFilterRequest $request): View
    {
        $data = $this->billingService->getSuperAdminBillingData($request->validated());
        return view('superadmin.billing.index', $data);
    }

    public function approveTransaction(int|string $id): JsonResponse|RedirectResponse
    {
        $result = $this->billingService->approveManualTransfer($id, Auth::user());
        if (request()->wantsJson()) {
            return response()->json($result['response'], $result['status_code'] ?? 200);
        }

        return redirect()->back()->with(
            $result['response']['success'] ? 'success' : 'error',
            $result['response']['message']
        );
    }

    public function rejectTransaction(RejectManualTransferRequest $request, int|string $id): JsonResponse|RedirectResponse
    {
        $reason = $request->input('reason');
        $result = $this->billingService->rejectManualTransfer($id, Auth::user(), $reason);
        if ($request->wantsJson()) {
            return response()->json($result['response'], $result['status_code'] ?? 200);
        }

        return redirect()->back()->with(
            $result['response']['success'] ? 'success' : 'error',
            $result['response']['message']
        );
    }

    public function togglePaymentMethod(string $methodKey): JsonResponse|RedirectResponse
    {
        $result = $this->billingService->togglePaymentMethod(Auth::user(), $methodKey);
        if (request()->wantsJson()) {
            return response()->json($result['response'], $result['status_code'] ?? 200);
        }

        return redirect()->back()->with(
            $result['response']['success'] ? 'success' : 'error',
            $result['response']['message']
        );
    }

    public function updatePaymentMethodSetting(UpdatePaymentMethodSettingRequest $request, string $methodKey): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $result = $this->billingService->updatePaymentMethodSetting(Auth::user(), $methodKey, $data);
        if ($request->wantsJson()) {
            return response()->json($result['response'], $result['status_code'] ?? 200);
        }

        return redirect()->back()->with(
            $result['response']['success'] ? 'success' : 'error',
            $result['response']['message']
        );
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

    public function switchPaymentMethod(SwitchPaymentMethodRequest $request, int|string $id): JsonResponse
    {
        $targetMethod = $request->input('target_method');
        $result = $this->billingService->switchPaymentMethod(Auth::user(), $id, $targetMethod);
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }

    public function cancelTransaction(CancelBillingTransactionRequest $request, int|string $id): JsonResponse
    {
        $reason = $request->input('reason');
        $result = $this->billingService->cancelBillingTransaction(Auth::user(), $id, $reason);
        return response()->json($result['response'], $result['status_code'] ?? 200);
    }
}
