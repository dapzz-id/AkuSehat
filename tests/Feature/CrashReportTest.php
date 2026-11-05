<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\AuditLog;
use App\Enums\AuditCategory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrashReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('user_role')->nullable();
                $table->uuid('instansi_id')->nullable();
                $table->string('instansi_name')->nullable();
                $table->string('action');
                $table->string('category');
                $table->text('description')->nullable();
                $table->json('properties')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_can_ingest_mobile_crash_report(): void
    {
        $payload = [
            'error_name' => 'TypeError',
            'error_message' => "Objects are not valid as a React child (found: object with keys {id, instansi_id})",
            'stack_trace' => "at RCTText (<anonymous>)\n at HomeScreenHealthConsultant",
            'component_stack' => "\n in RCTText\n in HomeScreenHealthConsultant",
            'app_version' => '1.1.4',
            'build_number' => 18,
            'platform' => 'android',
            'platform_version' => '12',
            'device_model' => 'SM-G991B',
            'device_brand' => 'Samsung',
            'user_id' => 'u-12345',
            'user_name' => 'Health Consultant Tels',
            'user_role' => 'Health Consultant',
            'instansi_name' => 'SMKS TELEKOMUNIKASI TELESANDI BEKASI',
            'screen_name' => 'HomeScreenHealthConsultant',
        ];

        $response = $this->postJson('/api/crash-reports', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'mobile_app_crash',
            'category' => AuditCategory::CRASH->value,
        ]);

        $log = AuditLog::where('action', 'mobile_app_crash')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Objects are not valid as a React child', $log->description);
        $this->assertEquals('1.1.4', $log->properties['app_version']);
        $this->assertEquals('SM-G991B', $log->properties['device_model']);
        $this->assertEquals('Samsung', $log->properties['device_brand']);
    }
}
