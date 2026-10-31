<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat database trigger Append-Only pada tabel audit_logs.
     * Mencegah aksi UPDATE dan DELETE di level engine PostgreSQL demi integritas audit & non-repudiation.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::unprepared("
                -- 1. Buat fungsi penolak modifikasi untuk tabel audit_logs
                CREATE OR REPLACE FUNCTION reject_audit_log_modification()
                RETURNS trigger AS \$\$
                BEGIN
                    RAISE EXCEPTION 'Tabel % bersifat Append-Only: Operasi % dilarang demi kepatuhan integritas audit & non-repudiation.', TG_TABLE_NAME, TG_OP;
                END;
                \$\$ LANGUAGE plpgsql;

                -- 2. Hapus trigger lama jika pernah ada
                DROP TRIGGER IF EXISTS trg_audit_logs_append_only ON audit_logs;

                -- 3. Pasang trigger sebelum UPDATE atau DELETE
                CREATE TRIGGER trg_audit_logs_append_only
                    BEFORE UPDATE OR DELETE ON audit_logs
                    FOR EACH ROW
                    EXECUTE FUNCTION reject_audit_log_modification();
            ");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_audit_logs_prevent_update;
                CREATE TRIGGER trg_audit_logs_prevent_update
                BEFORE UPDATE ON audit_logs
                FOR EACH ROW
                BEGIN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tabel audit_logs bersifat Append-Only: Operasi UPDATE dilarang demi kepatuhan integritas audit & non-repudiation.';
                END;
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_audit_logs_prevent_delete;
                CREATE TRIGGER trg_audit_logs_prevent_delete
                BEFORE DELETE ON audit_logs
                FOR EACH ROW
                BEGIN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tabel audit_logs bersifat Append-Only: Operasi DELETE dilarang demi kepatuhan integritas audit & non-repudiation.';
                END;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_audit_logs_append_only ON audit_logs;
                DROP FUNCTION IF EXISTS reject_audit_log_modification();
            ");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_audit_logs_prevent_update;
                DROP TRIGGER IF EXISTS trg_audit_logs_prevent_delete;
            ");
        }
    }
};
