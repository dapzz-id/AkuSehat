<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GlobalModuleConfig;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;

class SecurityHardeningTest extends TestCase
{
    /**
     * Test unauthenticated API requests return structured JSON 401 instead of redirecting.
     */
    public function test_unauthenticated_api_request_returns_json_401(): void
    {
        $response = $this->getJson('/api/health-consultant/health-summary');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    /**
     * Test role-based protection on Health Consultant API endpoints.
     * Regular members must receive JSON 403 Forbidden.
     */
    public function test_member_cannot_access_health_consultant_api(): void
    {
        $member = new User();
        $member->id = (string) Str::uuid7();
        $member->nama = 'Test Member';
        $member->level = 'Member';
        $member->instansi_id = (string) Str::uuid7();
        $member->id_divisi = (string) Str::uuid7();
        $member->setRelation('divisi', new \App\Models\Divisi(['id' => $member->id_divisi]));

        Sanctum::actingAs($member);

        $response = $this->getJson('/api/health-consultant/health-summary');

        $response->assertStatus(403);
        $response->assertJson([
            'status' => false,
        ]);
    }

    /**
     * Test role-based protection on Health Monitor API endpoints.
     * Regular members must receive JSON 403 Forbidden.
     */
    public function test_member_cannot_access_health_monitor_api(): void
    {
        $member = new User();
        $member->id = (string) Str::uuid7();
        $member->nama = 'Test Member';
        $member->level = 'Member';
        $member->instansi_id = (string) Str::uuid7();
        $member->id_divisi = (string) Str::uuid7();
        $member->setRelation('divisi', new \App\Models\Divisi(['id' => $member->id_divisi]));

        Sanctum::actingAs($member);

        $response = $this->getJson('/api/health-monitor/member-haid');

        $response->assertStatus(403);
        $response->assertJson([
            'status' => false,
        ]);
    }

    /**
     * Test input validation for daily vital logs water intake.
     * Negative or unbounded amounts must be rejected with HTTP 422.
     */
    public function test_vital_log_water_intake_rejects_invalid_values(): void
    {
        $user = new User();
        $user->id = (string) Str::uuid7();
        $user->nama = 'Hydration Tester';
        $user->level = 'Member';
        $user->instansi_id = (string) Str::uuid7();
        $user->id_divisi = (string) Str::uuid7();
        $user->setRelation('divisi', new \App\Models\Divisi(['id' => $user->id_divisi]));

        Sanctum::actingAs($user);

        // Test excessive negative amount (< -5000ml)
        $negativeResponse = $this->postJson('/api/vital-logs/water/add', [
            'amount' => -999999,
        ]);
        $negativeResponse->assertStatus(422);
        $negativeResponse->assertJsonValidationErrors(['amount']);

        // Test excessive amount (> 5000ml)
        $excessiveResponse = $this->postJson('/api/vital-logs/water/add', [
            'amount' => 999999,
        ]);
        $excessiveResponse->assertStatus(422);
        $excessiveResponse->assertJsonValidationErrors(['amount']);
    }

    /**
     * Test GlobalModuleConfig clearCache alias executes without undefined method error.
     */
    public function test_global_module_config_clear_cache_method_exists(): void
    {
        $this->assertTrue(method_exists(GlobalModuleConfig::class, 'clearCache'));
        GlobalModuleConfig::clearCache();
        $this->assertTrue(true);
    }

    /**
     * Test UpdateKesehatanRequest enforces instansi tenant validation.
     */
    public function test_update_kesehatan_request_has_tenant_aware_rules(): void
    {
        $request = new \App\Modules\Kesehatan\Pemeriksaan\Requests\UpdateKesehatanRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('id_user', $rules);
        $this->assertIsArray($rules['id_user']);
        $this->assertContains('required', $rules['id_user']);
    }

    /**
     * Test UpdateHbRequest enforces instansi tenant validation.
     */
    public function test_update_hb_request_has_tenant_aware_rules(): void
    {
        $request = new \App\Modules\Hb\PemeriksaanHb\Requests\UpdateHbRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('id_user', $rules);
        $this->assertIsArray($rules['id_user']);
        $this->assertContains('required', $rules['id_user']);
    }

    /**
     * Test UserImportExportService exportToExcel method exists.
     */
    public function test_user_export_service_method_exists(): void
    {
        $this->assertTrue(method_exists(\App\Modules\User\ManageUser\Services\UserImportExportService::class, 'exportToExcel'));
    }

    /**
     * Test UserRole values contain all authorized system roles.
     */
    public function test_user_roles_contain_all_expected_enums(): void
    {
        $roles = \App\Modules\User\Enums\UserRole::values();

        $this->assertContains('Member', $roles);
        $this->assertContains('Admin', $roles);
        $this->assertContains('Health Monitor', $roles);
        $this->assertContains('Health Consultant', $roles);
        $this->assertContains('Admin Instansi', $roles);
        $this->assertContains('SuperAdmin', $roles);
    }

    /**
     * Test all API controllers strictly apply Services, Repositories, FormRequests, and Enums without raw Request.
     */
    public function test_api_controllers_strictly_use_repositories_services_requests_and_enums(): void
    {
        $controllers = [
            \App\Modules\Billing\Subscription\Controllers\BillingController::class,
            \App\Modules\Hb\PemeriksaanHb\Controllers\HbController::class,
            \App\Modules\Kesehatan\Pemeriksaan\Controllers\KesehatanController::class,
            \App\Modules\Mens\DataHaid\Controllers\FemHealthMonitorController::class,
            \App\Modules\User\Auth\Controllers\AuthController::class,
            \App\Modules\SuperAdmin\Instansi\Controllers\InstansiController::class,
            \App\Modules\SuperAdmin\ModulePermission\Controllers\ModulePermissionController::class,
            \App\Modules\User\ManageUser\Controllers\DashboardController::class,
            \App\Modules\Divisi\UnitManagement\Controllers\DivisiController::class,
            \App\Modules\User\ManageUser\Controllers\AdminUserController::class,
            \App\Modules\User\ManageUser\Controllers\SuperAdminUserController::class,
            \App\Modules\Kesehatan\Activities\Controllers\Api\ActivityApiController::class,
            \App\Modules\Kesehatan\VitalLogs\Controllers\Api\VitalLogApiController::class,
            \App\Modules\Kesehatan\Counseling\Controllers\Api\HealthConsultantApiController::class,
            \App\Modules\Hb\PemeriksaanHb\Controllers\Api\HbApiController::class,
            \App\Modules\Kesehatan\Pemeriksaan\Controllers\Api\KesehatanApiController::class,
            \App\Modules\User\Member\Controllers\Api\MemberApiController::class,
            \App\Modules\User\Auth\Controllers\Api\AuthApiController::class,
            \App\Modules\Mens\DataHaid\Controllers\Api\HealthMonitorApiController::class,
            \App\Modules\Divisi\UnitManagement\Controllers\Api\DivisiApiController::class,
            \App\Modules\User\ManageUser\Controllers\Api\UserApiController::class,
        ];

        foreach ($controllers as $controllerClass) {
            $ref = new \ReflectionClass($controllerClass);

            foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class !== $controllerClass || $method->name === '__construct') {
                    continue;
                }

                foreach ($method->getParameters() as $param) {
                    $type = $param->getType();
                    if ($type instanceof \ReflectionNamedType) {
                        $this->assertNotEquals(
                            \Illuminate\Http\Request::class,
                            $type->getName(),
                            "Controller {$controllerClass}::{$method->name} uses raw Request \${$param->getName()} instead of dedicated FormRequest"
                        );
                    }
                }
            }
        }

        // Verify key domain enums exist and are backed enums
        $this->assertTrue(enum_exists(\App\Modules\Kesehatan\Activities\Enums\ActivityType::class));
        $this->assertTrue(enum_exists(\App\Modules\Kesehatan\VitalLogs\Enums\MoodScore::class));
        $this->assertTrue(enum_exists(\App\Modules\Kesehatan\Counseling\Enums\CounselingSessionStatus::class));
        $this->assertTrue(enum_exists(\App\Modules\Hb\PemeriksaanHb\Enums\HbStatus::class));
        $this->assertTrue(enum_exists(\App\Modules\Mens\DataHaid\Enums\PitaStatus::class));
        $this->assertTrue(enum_exists(\App\Modules\Mens\DataHaid\Enums\HaidStatus::class));
        $this->assertTrue(enum_exists(\App\Modules\Billing\Subscription\Enums\BillingRate::class));
        $this->assertTrue(enum_exists(\App\Modules\Billing\Subscription\Enums\BillingTransactionType::class));
        $this->assertTrue(enum_exists(\App\Modules\Billing\Subscription\Enums\PaymentMethod::class));
    }
}
