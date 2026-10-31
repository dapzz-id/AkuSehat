<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // -------------------------------------------------------------
        // 1. COMPOSITE INDEXES FOR FAST MULTI-TENANT QUERIES
        // (Cross-Database: Compatible with PostgreSQL, MySQL, and MariaDB)
        // -------------------------------------------------------------
        $indexes = [
            'users' => [
                ['columns' => ['instansi_id', 'level', 'id_divisi'], 'name' => 'idx_users_instansi_level_divisi'],
                ['columns' => ['instansi_id', 'nomor_induk'], 'name' => 'idx_users_instansi_nomor_induk'],
            ],
            'kesehatan' => [
                ['columns' => ['instansi_id', 'tgl'], 'name' => 'idx_kesehatan_instansi_tgl'],
                ['columns' => ['id_user', 'tgl'], 'name' => 'idx_kesehatan_user_tgl'],
                ['columns' => ['instansi_id', 'status'], 'name' => 'idx_kesehatan_instansi_status'],
                ['columns' => ['instansi_id', 'status_darah'], 'name' => 'idx_kesehatan_instansi_status_darah'],
            ],
            'hb' => [
                ['columns' => ['instansi_id', 'tgl'], 'name' => 'idx_hb_instansi_tgl'],
                ['columns' => ['instansi_id', 'id_divisi'], 'name' => 'idx_hb_instansi_divisi'],
                ['columns' => ['id_user', 'tgl'], 'name' => 'idx_hb_user_tgl'],
            ],
            'counseling_sessions' => [
                ['columns' => ['instansi_id', 'session_date'], 'name' => 'idx_counseling_instansi_date'],
                ['columns' => ['instansi_id', 'status'], 'name' => 'idx_counseling_instansi_status'],
            ],
            'user_activities' => [
                ['columns' => ['instansi_id', 'started_at'], 'name' => 'idx_activities_instansi_started'],
            ],
            'user_vital_logs' => [
                ['columns' => ['instansi_id', 'log_date'], 'name' => 'idx_vitals_instansi_log_date'],
            ],
            'divisi' => [
                ['columns' => ['instansi_id'], 'name' => 'idx_divisi_instansi'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (!Schema::hasTable($table)) continue;
            foreach ($tableIndexes as $idx) {
                if (!Schema::hasIndex($table, $idx['name'])) {
                    try {
                        Schema::table($table, function (Blueprint $t) use ($idx) {
                            $t->index($idx['columns'], $idx['name']);
                        });
                    } catch (\Throwable $e) {}
                }
            }
        }

        // -------------------------------------------------------------
        // 2. DATABASE FUNCTIONS (POSTGRESQL & MYSQL VERSIONS)
        // -------------------------------------------------------------
        if ($driver === 'pgsql') {
            // Function 2.1: Hitung Nilai IMT (BMI)
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_imt(p_bb NUMERIC, p_tb NUMERIC)
                RETURNS NUMERIC AS $$
                BEGIN
                    IF p_bb IS NULL OR p_tb IS NULL OR p_tb <= 0 THEN
                        RETURN NULL;
                    END IF;
                    RETURN ROUND(p_bb / ((p_tb / 100.0) * (p_tb / 100.0)), 2);
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            // Function 2.2: Kategori Status IMT WHO
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_status_imt(p_imt NUMERIC)
                RETURNS VARCHAR AS $$
                BEGIN
                    IF p_imt IS NULL THEN
                        RETURN NULL;
                    ELSIF p_imt < 18.5 THEN
                        RETURN 'Underweight';
                    ELSIF p_imt < 25.0 THEN
                        RETURN 'Normal';
                    ELSIF p_imt < 30.0 THEN
                        RETURN 'Overweight';
                    ELSIF p_imt < 35.0 THEN
                        RETURN 'Obesitas Level 1';
                    ELSIF p_imt < 40.0 THEN
                        RETURN 'Obesitas Level 2';
                    ELSE
                        RETURN 'Obesitas Level 3';
                    END IF;
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            // Function 2.3: Pesan / Rekomendasi Klinis IMT
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_pesan_imt(p_status VARCHAR)
                RETURNS TEXT AS $$
                BEGIN
                    CASE p_status
                        WHEN 'Underweight' THEN
                            RETURN 'Berat badan kurang. Tingkatkan asupan nutrisi secara seimbang.';
                        WHEN 'Overweight' THEN
                            RETURN 'Berat badan berlebih. Disarankan mengatur pola makan dan olahraga rutin.';
                        WHEN 'Obesitas Level 1', 'Obesitas Level 2', 'Obesitas Level 3' THEN
                            RETURN 'Obesitas. Disarankan konsultasi dengan ahli gizi untuk penanganan lebih lanjut.';
                        ELSE
                            RETURN 'Berat badan normal. Pertahankan pola hidup sehat.';
                    END CASE;
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            // Function 2.4: Status Tekanan Darah WHO / AHA
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_status_darah(p_sistol INT, p_diastol INT)
                RETURNS VARCHAR AS $$
                BEGIN
                    IF p_sistol IS NULL OR p_diastol IS NULL THEN
                        RETURN NULL;
                    ELSIF p_sistol < 90 OR p_diastol < 60 THEN
                        RETURN 'Hipotensi';
                    ELSIF p_sistol < 120 AND p_diastol < 80 THEN
                        RETURN 'Normal';
                    ELSIF p_sistol >= 120 AND p_sistol < 130 AND p_diastol < 80 THEN
                        RETURN 'Elevasi';
                    ELSIF (p_sistol >= 130 AND p_sistol < 140) OR (p_diastol >= 80 AND p_diastol < 90) THEN
                        RETURN 'Hipertensi Tahap 1';
                    ELSIF (p_sistol >= 140 AND p_sistol < 180) OR (p_diastol >= 90 AND p_diastol < 120) THEN
                        RETURN 'Hipertensi Tahap 2';
                    ELSE
                        RETURN 'Krisis';
                    END IF;
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            // Function 2.5: Pesan / Rekomendasi Tekanan Darah
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_pesan_darah(p_status VARCHAR)
                RETURNS TEXT AS $$
                BEGIN
                    CASE p_status
                        WHEN 'Hipotensi' THEN
                            RETURN 'Tekanan darah rendah. Pastikan hidrasi cukup dan istirahat memadai.';
                        WHEN 'Elevasi' THEN
                            RETURN 'Tekanan darah sedikit meningkat. Harap perhatikan gaya hidup sehat.';
                        WHEN 'Hipertensi Tahap 1' THEN
                            RETURN 'Hipertensi tahap 1. Mulai lakukan pengurangan garam dan olahraga teratur.';
                        WHEN 'Hipertensi Tahap 2' THEN
                            RETURN 'Hipertensi tahap 2. Perlu konsultasi lebih lanjut dengan tenaga medis.';
                        WHEN 'Krisis' THEN
                            RETURN 'Kondisi darurat! Segera hubungi tenaga medis.';
                        ELSE
                            RETURN 'Tekanan darah normal. Pertahankan gaya hidup sehat.';
                    END CASE;
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            // Function 2.6: Status Hemoglobin Berdasarkan Gender
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_status_hb(p_hb NUMERIC, p_jk VARCHAR)
                RETURNS VARCHAR AS $$
                BEGIN
                    IF p_hb IS NULL THEN
                        RETURN NULL;
                    END IF;

                    IF p_jk = 'P' THEN
                        IF p_hb < 12.0 THEN
                            RETURN 'Anemia';
                        ELSIF p_hb > 15.0 THEN
                            RETURN 'Tinggi';
                        ELSE
                            RETURN 'Normal';
                        END IF;
                    ELSE
                        IF p_hb < 13.0 THEN
                            RETURN 'Anemia';
                        ELSIF p_hb > 17.0 THEN
                            RETURN 'Tinggi';
                        ELSE
                            RETURN 'Normal';
                        END IF;
                    END IF;
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");

            // Function 2.7: Pesan Hemoglobin
            DB::statement("
                CREATE OR REPLACE FUNCTION fn_calculate_pesan_hb(p_status VARCHAR)
                RETURNS TEXT AS $$
                BEGIN
                    CASE p_status
                        WHEN 'Anemia' THEN
                            RETURN 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.';
                        WHEN 'Tinggi' THEN
                            RETURN 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.';
                        ELSE
                            RETURN 'Kadar hemoglobin normal.';
                    END CASE;
                END;
                $$ LANGUAGE plpgsql IMMUTABLE;
            ");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // Function 2.1 MySQL: Hitung IMT
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_imt;
                CREATE FUNCTION fn_calculate_imt(p_bb DECIMAL(8,2), p_tb DECIMAL(8,2))
                RETURNS DECIMAL(8,2)
                DETERMINISTIC
                BEGIN
                    IF p_bb IS NULL OR p_tb IS NULL OR p_tb <= 0 THEN
                        RETURN NULL;
                    END IF;
                    RETURN ROUND(p_bb / ((p_tb / 100.0) * (p_tb / 100.0)), 2);
                END;
            ");

            // Function 2.2 MySQL: Status IMT
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_status_imt;
                CREATE FUNCTION fn_calculate_status_imt(p_imt DECIMAL(8,2))
                RETURNS VARCHAR(50)
                DETERMINISTIC
                BEGIN
                    IF p_imt IS NULL THEN
                        RETURN NULL;
                    ELSEIF p_imt < 18.5 THEN
                        RETURN 'Underweight';
                    ELSEIF p_imt < 25.0 THEN
                        RETURN 'Normal';
                    ELSEIF p_imt < 30.0 THEN
                        RETURN 'Overweight';
                    ELSEIF p_imt < 35.0 THEN
                        RETURN 'Obesitas Level 1';
                    ELSEIF p_imt < 40.0 THEN
                        RETURN 'Obesitas Level 2';
                    ELSE
                        RETURN 'Obesitas Level 3';
                    END IF;
                END;
            ");

            // Function 2.3 MySQL: Pesan IMT
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_pesan_imt;
                CREATE FUNCTION fn_calculate_pesan_imt(p_status VARCHAR(50))
                RETURNS TEXT
                DETERMINISTIC
                BEGIN
                    CASE p_status
                        WHEN 'Underweight' THEN
                            RETURN 'Berat badan kurang. Tingkatkan asupan nutrisi secara seimbang.';
                        WHEN 'Overweight' THEN
                            RETURN 'Berat badan berlebih. Disarankan mengatur pola makan dan olahraga rutin.';
                        WHEN 'Obesitas Level 1' THEN
                            RETURN 'Obesitas. Disarankan konsultasi dengan ahli gizi untuk penanganan lebih lanjut.';
                        WHEN 'Obesitas Level 2' THEN
                            RETURN 'Obesitas. Disarankan konsultasi dengan ahli gizi untuk penanganan lebih lanjut.';
                        WHEN 'Obesitas Level 3' THEN
                            RETURN 'Obesitas. Disarankan konsultasi dengan ahli gizi untuk penanganan lebih lanjut.';
                        ELSE
                            RETURN 'Berat badan normal. Pertahankan pola hidup sehat.';
                    END CASE;
                END;
            ");

            // Function 2.4 MySQL: Status Tekanan Darah
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_status_darah;
                CREATE FUNCTION fn_calculate_status_darah(p_sistol INT, p_diastol INT)
                RETURNS VARCHAR(50)
                DETERMINISTIC
                BEGIN
                    IF p_sistol IS NULL OR p_diastol IS NULL THEN
                        RETURN NULL;
                    ELSEIF p_sistol < 90 OR p_diastol < 60 THEN
                        RETURN 'Hipotensi';
                    ELSEIF p_sistol < 120 AND p_diastol < 80 THEN
                        RETURN 'Normal';
                    ELSEIF p_sistol >= 120 AND p_sistol < 130 AND p_diastol < 80 THEN
                        RETURN 'Elevasi';
                    ELSEIF (p_sistol >= 130 AND p_sistol < 140) OR (p_diastol >= 80 AND p_diastol < 90) THEN
                        RETURN 'Hipertensi Tahap 1';
                    ELSEIF (p_sistol >= 140 AND p_sistol < 180) OR (p_diastol >= 90 AND p_diastol < 120) THEN
                        RETURN 'Hipertensi Tahap 2';
                    ELSE
                        RETURN 'Krisis';
                    END IF;
                END;
            ");

            // Function 2.5 MySQL: Pesan Tekanan Darah
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_pesan_darah;
                CREATE FUNCTION fn_calculate_pesan_darah(p_status VARCHAR(50))
                RETURNS TEXT
                DETERMINISTIC
                BEGIN
                    CASE p_status
                        WHEN 'Hipotensi' THEN
                            RETURN 'Tekanan darah rendah. Pastikan hidrasi cukup dan istirahat memadai.';
                        WHEN 'Elevasi' THEN
                            RETURN 'Tekanan darah sedikit meningkat. Harap perhatikan gaya hidup sehat.';
                        WHEN 'Hipertensi Tahap 1' THEN
                            RETURN 'Hipertensi tahap 1. Mulai lakukan pengurangan garam dan olahraga teratur.';
                        WHEN 'Hipertensi Tahap 2' THEN
                            RETURN 'Hipertensi tahap 2. Perlu konsultasi lebih lanjut dengan tenaga medis.';
                        WHEN 'Krisis' THEN
                            RETURN 'Kondisi darurat! Segera hubungi tenaga medis.';
                        ELSE
                            RETURN 'Tekanan darah normal. Pertahankan gaya hidup sehat.';
                    END CASE;
                END;
            ");

            // Function 2.6 MySQL: Status Hemoglobin
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_status_hb;
                CREATE FUNCTION fn_calculate_status_hb(p_hb DECIMAL(8,2), p_jk VARCHAR(10))
                RETURNS VARCHAR(50)
                DETERMINISTIC
                BEGIN
                    IF p_hb IS NULL THEN
                        RETURN NULL;
                    END IF;

                    IF p_jk = 'P' THEN
                        IF p_hb < 12.0 THEN
                            RETURN 'Anemia';
                        ELSEIF p_hb > 15.0 THEN
                            RETURN 'Tinggi';
                        ELSE
                            RETURN 'Normal';
                        END IF;
                    ELSE
                        IF p_hb < 13.0 THEN
                            RETURN 'Anemia';
                        ELSEIF p_hb > 17.0 THEN
                            RETURN 'Tinggi';
                        ELSE
                            RETURN 'Normal';
                        END IF;
                    END IF;
                END;
            ");

            // Function 2.7 MySQL: Pesan Hemoglobin
            DB::unprepared("
                DROP FUNCTION IF EXISTS fn_calculate_pesan_hb;
                CREATE FUNCTION fn_calculate_pesan_hb(p_status VARCHAR(50))
                RETURNS TEXT
                DETERMINISTIC
                BEGIN
                    CASE p_status
                        WHEN 'Anemia' THEN
                            RETURN 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.';
                        WHEN 'Tinggi' THEN
                            RETURN 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.';
                        ELSE
                            RETURN 'Kadar hemoglobin normal.';
                    END CASE;
                END;
            ");
        }

        // -------------------------------------------------------------
        // 3. DATABASE TRIGGERS (AUTO COMPUTE ON INSERT / UPDATE)
        // -------------------------------------------------------------
        if ($driver === 'pgsql') {
            DB::statement("
                CREATE OR REPLACE FUNCTION trg_fn_kesehatan_auto_compute()
                RETURNS TRIGGER AS $$
                DECLARE
                    v_user_instansi UUID;
                BEGIN
                    IF NEW.instansi_id IS NULL AND NEW.id_user IS NOT NULL THEN
                        SELECT instansi_id INTO v_user_instansi FROM users WHERE id = NEW.id_user;
                        NEW.instansi_id := v_user_instansi;
                    END IF;

                    IF NEW.bb IS NOT NULL AND NEW.tb IS NOT NULL AND NEW.tb > 0 THEN
                        NEW.imt := fn_calculate_imt(CAST(NEW.bb AS NUMERIC), CAST(NEW.tb AS NUMERIC));
                        NEW.status := fn_calculate_status_imt(NEW.imt);
                        NEW.pesan_imt := fn_calculate_pesan_imt(NEW.status);
                    END IF;

                    IF NEW.sistol IS NOT NULL AND NEW.diastol IS NOT NULL THEN
                        NEW.status_darah := fn_calculate_status_darah(NEW.sistol, NEW.diastol);
                        NEW.pesan_tkd := fn_calculate_pesan_darah(NEW.status_darah);
                    END IF;

                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ");

            DB::statement("DROP TRIGGER IF EXISTS trg_kesehatan_auto_compute ON kesehatan;");
            DB::statement("
                CREATE TRIGGER trg_kesehatan_auto_compute
                BEFORE INSERT OR UPDATE ON kesehatan
                FOR EACH ROW
                EXECUTE FUNCTION trg_fn_kesehatan_auto_compute();
            ");

            DB::statement("
                CREATE OR REPLACE FUNCTION trg_fn_hb_auto_compute()
                RETURNS TRIGGER AS $$
                DECLARE
                    v_user_jk VARCHAR(10);
                    v_user_instansi UUID;
                    v_user_divisi UUID;
                BEGIN
                    IF NEW.id_user IS NOT NULL THEN
                        SELECT jk, instansi_id, id_divisi 
                        INTO v_user_jk, v_user_instansi, v_user_divisi 
                        FROM users 
                        WHERE id = NEW.id_user;

                        IF NEW.instansi_id IS NULL THEN
                            NEW.instansi_id := v_user_instansi;
                        END IF;

                        IF NEW.id_divisi IS NULL THEN
                            NEW.id_divisi := v_user_divisi;
                        END IF;
                    END IF;

                    IF NEW.hb IS NOT NULL THEN
                        NEW.status := fn_calculate_status_hb(CAST(NEW.hb AS NUMERIC), COALESCE(v_user_jk, 'L'));
                        NEW.pesan := fn_calculate_pesan_hb(NEW.status);
                    END IF;

                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ");

            DB::statement("DROP TRIGGER IF EXISTS trg_hb_auto_compute ON hb;");
            DB::statement("
                CREATE TRIGGER trg_hb_auto_compute
                BEFORE INSERT OR UPDATE ON hb
                FOR EACH ROW
                EXECUTE FUNCTION trg_fn_hb_auto_compute();
            ");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // MySQL: Triggers on kesehatan (BEFORE INSERT & BEFORE UPDATE)
            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_kesehatan_auto_compute_insert;
                CREATE TRIGGER trg_kesehatan_auto_compute_insert
                BEFORE INSERT ON kesehatan
                FOR EACH ROW
                BEGIN
                    DECLARE v_user_instansi CHAR(36);

                    IF NEW.instansi_id IS NULL AND NEW.id_user IS NOT NULL THEN
                        SELECT instansi_id INTO v_user_instansi FROM users WHERE id = NEW.id_user LIMIT 1;
                        SET NEW.instansi_id = v_user_instansi;
                    END IF;

                    IF NEW.bb IS NOT NULL AND NEW.tb IS NOT NULL AND NEW.tb > 0 THEN
                        SET NEW.imt = fn_calculate_imt(NEW.bb, NEW.tb);
                        SET NEW.status = fn_calculate_status_imt(NEW.imt);
                        SET NEW.pesan_imt = fn_calculate_pesan_imt(NEW.status);
                    END IF;

                    IF NEW.sistol IS NOT NULL AND NEW.diastol IS NOT NULL THEN
                        SET NEW.status_darah = fn_calculate_status_darah(NEW.sistol, NEW.diastol);
                        SET NEW.pesan_tkd = fn_calculate_pesan_darah(NEW.status_darah);
                    END IF;
                END;
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_kesehatan_auto_compute_update;
                CREATE TRIGGER trg_kesehatan_auto_compute_update
                BEFORE UPDATE ON kesehatan
                FOR EACH ROW
                BEGIN
                    DECLARE v_user_instansi CHAR(36);

                    IF NEW.instansi_id IS NULL AND NEW.id_user IS NOT NULL THEN
                        SELECT instansi_id INTO v_user_instansi FROM users WHERE id = NEW.id_user LIMIT 1;
                        SET NEW.instansi_id = v_user_instansi;
                    END IF;

                    IF NEW.bb IS NOT NULL AND NEW.tb IS NOT NULL AND NEW.tb > 0 THEN
                        SET NEW.imt = fn_calculate_imt(NEW.bb, NEW.tb);
                        SET NEW.status = fn_calculate_status_imt(NEW.imt);
                        SET NEW.pesan_imt = fn_calculate_pesan_imt(NEW.status);
                    END IF;

                    IF NEW.sistol IS NOT NULL AND NEW.diastol IS NOT NULL THEN
                        SET NEW.status_darah = fn_calculate_status_darah(NEW.sistol, NEW.diastol);
                        SET NEW.pesan_tkd = fn_calculate_pesan_darah(NEW.status_darah);
                    END IF;
                END;
            ");

            // MySQL: Triggers on hb (BEFORE INSERT & BEFORE UPDATE)
            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_hb_auto_compute_insert;
                CREATE TRIGGER trg_hb_auto_compute_insert
                BEFORE INSERT ON hb
                FOR EACH ROW
                BEGIN
                    DECLARE v_user_jk VARCHAR(10);
                    DECLARE v_user_instansi CHAR(36);
                    DECLARE v_user_divisi CHAR(36);

                    IF NEW.id_user IS NOT NULL THEN
                        SELECT jk, instansi_id, id_divisi 
                        INTO v_user_jk, v_user_instansi, v_user_divisi 
                        FROM users 
                        WHERE id = NEW.id_user LIMIT 1;

                        IF NEW.instansi_id IS NULL THEN
                            SET NEW.instansi_id = v_user_instansi;
                        END IF;

                        IF NEW.id_divisi IS NULL THEN
                            SET NEW.id_divisi = v_user_divisi;
                        END IF;
                    END IF;

                    IF NEW.hb IS NOT NULL THEN
                        SET NEW.status = fn_calculate_status_hb(NEW.hb, COALESCE(v_user_jk, 'L'));
                        SET NEW.pesan = fn_calculate_pesan_hb(NEW.status);
                    END IF;
                END;
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS trg_hb_auto_compute_update;
                CREATE TRIGGER trg_hb_auto_compute_update
                BEFORE UPDATE ON hb
                FOR EACH ROW
                BEGIN
                    DECLARE v_user_jk VARCHAR(10);
                    DECLARE v_user_instansi CHAR(36);
                    DECLARE v_user_divisi CHAR(36);

                    IF NEW.id_user IS NOT NULL THEN
                        SELECT jk, instansi_id, id_divisi 
                        INTO v_user_jk, v_user_instansi, v_user_divisi 
                        FROM users 
                        WHERE id = NEW.id_user LIMIT 1;

                        IF NEW.instansi_id IS NULL THEN
                            SET NEW.instansi_id = v_user_instansi;
                        END IF;

                        IF NEW.id_divisi IS NULL THEN
                            SET NEW.id_divisi = v_user_divisi;
                        END IF;
                    END IF;

                    IF NEW.hb IS NOT NULL THEN
                        SET NEW.status = fn_calculate_status_hb(NEW.hb, COALESCE(v_user_jk, 'L'));
                        SET NEW.pesan = fn_calculate_pesan_hb(NEW.status);
                    END IF;
                END;
            ");
        }

        // -------------------------------------------------------------
        // 4. DATABASE VIEWS (HIGH SPEED AGGREGATION & ZERO PHP MEMORY)
        // (ANSI SQL standard - 100% identical and supported on PostgreSQL, MySQL, MariaDB)
        // -------------------------------------------------------------

        // View 4.1: Ringkasan Metrik Divisi untuk Dashboard Admin
        DB::statement("
            CREATE OR REPLACE VIEW v_divisi_dashboard_stats AS
            SELECT 
                d.id AS divisi_id,
                d.instansi_id,
                d.divisi_name,
                d.kategori,
                d.color_cover,
                d.created_at,
                COUNT(DISTINCT CASE WHEN u.level = 'Member' THEN u.id END) AS total_member,
                COUNT(DISTINCT CASE WHEN k.tgl IS NOT NULL AND EXTRACT(YEAR FROM k.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN k.id_kesehatan END) AS total_kesehatan_tahun_ini,
                COUNT(DISTINCT CASE WHEN h.tgl IS NOT NULL AND EXTRACT(YEAR FROM h.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN h.id_hb END) AS total_hb_tahun_ini,
                ROUND(AVG(CAST(k.imt AS DECIMAL(8,2))), 1) AS avg_imt,
                ROUND(AVG(CAST(h.hb AS DECIMAL(8,2))), 1) AS avg_hb,
                COUNT(DISTINCT CASE WHEN h.status = 'Anemia' AND EXTRACT(YEAR FROM h.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN h.id_hb END) AS total_anemia_tahun_ini
            FROM divisi d
            LEFT JOIN users u ON u.id_divisi = d.id AND u.level = 'Member'
            LEFT JOIN kesehatan k ON k.id_user = u.id
            LEFT JOIN hb h ON h.id_user = u.id
            GROUP BY d.id, d.instansi_id, d.divisi_name, d.kategori, d.color_cover, d.created_at;
        ");

        // View 4.2: Ringkasan Kesehatan Instansi (KPI Utama Satu Instansi)
        DB::statement("
            CREATE OR REPLACE VIEW v_instansi_health_summary AS
            SELECT 
                i.id AS instansi_id,
                i.nama_instansi,
                i.jenis_instansi,
                COUNT(DISTINCT d.id) AS total_divisi,
                COUNT(DISTINCT CASE WHEN u.level = 'Member' THEN u.id END) AS total_member,
                COUNT(DISTINCT u.id) AS total_all_users,
                COUNT(DISTINCT CASE WHEN k.tgl IS NOT NULL AND EXTRACT(YEAR FROM k.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN k.id_kesehatan END) AS total_kesehatan_tahun_ini,
                COUNT(DISTINCT CASE WHEN h.tgl IS NOT NULL AND EXTRACT(YEAR FROM h.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN h.id_hb END) AS total_hb_tahun_ini,
                COUNT(DISTINCT CASE WHEN h.status = 'Anemia' AND EXTRACT(YEAR FROM h.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN h.id_hb END) AS total_anemia_tahun_ini,
                COUNT(DISTINCT CASE WHEN k.status = 'Normal' AND EXTRACT(YEAR FROM k.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN k.id_kesehatan END) AS total_normal_imt_tahun_ini,
                COUNT(DISTINCT CASE WHEN k.status_darah LIKE 'Hipertensi%' AND EXTRACT(YEAR FROM k.tgl) = EXTRACT(YEAR FROM CURRENT_DATE) THEN k.id_kesehatan END) AS total_hipertensi_tahun_ini
            FROM instansi i
            LEFT JOIN divisi d ON d.instansi_id = i.id
            LEFT JOIN users u ON u.instansi_id = i.id
            LEFT JOIN kesehatan k ON k.instansi_id = i.id
            LEFT JOIN hb h ON h.instansi_id = i.id
            GROUP BY i.id, i.nama_instansi, i.jenis_instansi;
        ");

        // View 4.3: Ringkasan SuperAdmin (Statistik Global Seluruh Instansi & Lisensi)
        DB::statement("
            CREATE OR REPLACE VIEW v_superadmin_overview AS
            SELECT 
                i.id AS instansi_id,
                i.nama_instansi,
                i.jenis_instansi,
                lk.key AS license_key,
                lk.status AS license_status,
                lk.kuota_pengguna,
                lk.tanggal_berakhir AS license_expires_at,
                COUNT(DISTINCT u.id) AS total_users,
                COUNT(DISTINCT d.id) AS total_divisi,
                i.created_at
            FROM instansi i
            LEFT JOIN license_keys lk ON lk.id = i.license_id_active
            LEFT JOIN users u ON u.instansi_id = i.id
            LEFT JOIN divisi d ON d.instansi_id = i.id
            GROUP BY i.id, i.nama_instansi, i.jenis_instansi, lk.key, lk.status, lk.kuota_pengguna, lk.tanggal_berakhir, i.created_at;
        ");

        // -------------------------------------------------------------
        // 5. STORED PROCEDURES (OPERASI ATOMIK & PEMELIHARAAN SERVER)
        // -------------------------------------------------------------
        if ($driver === 'pgsql') {
            DB::statement("
                CREATE OR REPLACE PROCEDURE sp_maintenance_cleanup_expired_tokens()
                LANGUAGE plpgsql AS $$
                DECLARE
                    v_deleted_tokens INT;
                    v_deleted_sessions INT;
                BEGIN
                    DELETE FROM personal_access_tokens 
                    WHERE last_used_at IS NOT NULL 
                      AND last_used_at < NOW() - INTERVAL '30 days';
                    GET DIAGNOSTICS v_deleted_tokens = ROW_COUNT;

                    DELETE FROM sessions 
                    WHERE last_activity < EXTRACT(EPOCH FROM (NOW() - INTERVAL '7 days'));
                    GET DIAGNOSTICS v_deleted_sessions = ROW_COUNT;

                    RAISE NOTICE 'Maintenance completed: % tokens and % sessions cleaned.', v_deleted_tokens, v_deleted_sessions;
                END;
                $$;
            ");

            DB::statement("
                CREATE OR REPLACE PROCEDURE sp_register_member_fast(
                    IN p_instansi_id UUID,
                    IN p_id_divisi UUID,
                    IN p_nama VARCHAR,
                    IN p_username VARCHAR,
                    IN p_email VARCHAR,
                    IN p_password_hash VARCHAR,
                    IN p_jk VARCHAR,
                    IN p_nomor_induk VARCHAR,
                    OUT p_new_user_id UUID
                )
                LANGUAGE plpgsql AS $$
                BEGIN
                    p_new_user_id := gen_random_uuid();

                    INSERT INTO users (
                        id, instansi_id, id_divisi, tgl, nama, username, email, 
                        password, jk, nomor_induk, level, created_at, updated_at
                    ) VALUES (
                        p_new_user_id, p_instansi_id, p_id_divisi, CAST(CURRENT_DATE AS VARCHAR), p_nama, p_username, p_email,
                        p_password_hash, p_jk, p_nomor_induk, 'Member', NOW(), NOW()
                    );

                    INSERT INTO user_activities (
                        id, instansi_id, user_id, activity_type, title, started_at, steps, calories, distance_meters, created_at, updated_at
                    ) VALUES (
                        gen_random_uuid(), p_instansi_id, p_new_user_id, 'run', 'Registrasi Akun Baru', NOW(), 0, 0, 0, NOW(), NOW()
                    );
                END;
                $$;
            ");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::unprepared("
                DROP PROCEDURE IF EXISTS sp_maintenance_cleanup_expired_tokens;
                CREATE PROCEDURE sp_maintenance_cleanup_expired_tokens()
                BEGIN
                    DELETE FROM personal_access_tokens 
                    WHERE last_used_at IS NOT NULL 
                      AND last_used_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

                    DELETE FROM sessions 
                    WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY));
                END;
            ");

            DB::unprepared("
                DROP PROCEDURE IF EXISTS sp_register_member_fast;
                CREATE PROCEDURE sp_register_member_fast(
                    IN p_instansi_id VARCHAR(36),
                    IN p_id_divisi VARCHAR(36),
                    IN p_nama VARCHAR(255),
                    IN p_username VARCHAR(255),
                    IN p_email VARCHAR(255),
                    IN p_password_hash VARCHAR(255),
                    IN p_jk VARCHAR(10),
                    IN p_nomor_induk VARCHAR(255),
                    OUT p_new_user_id VARCHAR(36)
                )
                BEGIN
                    SET p_new_user_id = UUID();

                    INSERT INTO users (
                        id, instansi_id, id_divisi, tgl, nama, username, email, 
                        password, jk, nomor_induk, level, created_at, updated_at
                    ) VALUES (
                        p_new_user_id, p_instansi_id, p_id_divisi, CAST(CURRENT_DATE AS CHAR), p_nama, p_username, p_email,
                        p_password_hash, p_jk, p_nomor_induk, 'Member', NOW(), NOW()
                    );

                    INSERT INTO user_activities (
                        id, instansi_id, user_id, activity_type, title, started_at, steps, calories, distance_meters, created_at, updated_at
                    ) VALUES (
                        UUID(), p_instansi_id, p_new_user_id, 'run', 'Registrasi Akun Baru', NOW(), 0, 0, 0, NOW(), NOW()
                    );
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

        // Drop Procedures
        if ($driver === 'pgsql') {
            DB::statement('DROP PROCEDURE IF EXISTS sp_register_member_fast;');
            DB::statement('DROP PROCEDURE IF EXISTS sp_maintenance_cleanup_expired_tokens;');
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_register_member_fast;');
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_maintenance_cleanup_expired_tokens;');
        }

        // Drop Views
        DB::statement('DROP VIEW IF EXISTS v_superadmin_overview;');
        DB::statement('DROP VIEW IF EXISTS v_instansi_health_summary;');
        DB::statement('DROP VIEW IF EXISTS v_divisi_dashboard_stats;');

        // Drop Triggers & Trigger Functions
        if ($driver === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS trg_hb_auto_compute ON hb;');
            DB::statement('DROP FUNCTION IF EXISTS trg_fn_hb_auto_compute;');

            DB::statement('DROP TRIGGER IF EXISTS trg_kesehatan_auto_compute ON kesehatan;');
            DB::statement('DROP FUNCTION IF EXISTS trg_fn_kesehatan_auto_compute;');

            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_pesan_hb;');
            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_status_hb;');
            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_pesan_darah;');
            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_status_darah;');
            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_pesan_imt;');
            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_status_imt;');
            DB::statement('DROP FUNCTION IF EXISTS fn_calculate_imt;');
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_hb_auto_compute_insert;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_hb_auto_compute_update;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_kesehatan_auto_compute_insert;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_kesehatan_auto_compute_update;');

            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_pesan_hb;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_status_hb;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_pesan_darah;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_status_darah;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_pesan_imt;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_status_imt;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_imt;');
        }

        // Drop Indexes
        $indexes = [
            'divisi' => ['idx_divisi_instansi'],
            'user_vital_logs' => ['idx_vitals_instansi_log_date'],
            'user_activities' => ['idx_activities_instansi_started'],
            'counseling_sessions' => ['idx_counseling_instansi_status', 'idx_counseling_instansi_date'],
            'hb' => ['idx_hb_user_tgl', 'idx_hb_instansi_divisi', 'idx_hb_instansi_tgl'],
            'kesehatan' => ['idx_kesehatan_instansi_status_darah', 'idx_kesehatan_instansi_status', 'idx_kesehatan_user_tgl', 'idx_kesehatan_instansi_tgl'],
            'users' => ['idx_users_instansi_nomor_induk', 'idx_users_instansi_level_divisi'],
        ];

        foreach ($indexes as $table => $indexNames) {
            if (!Schema::hasTable($table)) continue;
            foreach ($indexNames as $idxName) {
                if (Schema::hasIndex($table, $idxName)) {
                    try {
                        Schema::table($table, function (Blueprint $t) use ($idxName) {
                            $t->dropIndex($idxName);
                        });
                    } catch (\Throwable $e) {}
                }
            }
        }
    }
};
