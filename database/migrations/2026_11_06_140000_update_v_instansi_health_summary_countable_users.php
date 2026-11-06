<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP VIEW IF EXISTS v_instansi_health_summary;');
            DB::statement("
                CREATE VIEW v_instansi_health_summary AS
                SELECT 
                    i.id AS instansi_id,
                    i.nama_instansi,
                    i.jenis_instansi,
                    COUNT(DISTINCT d.id) AS total_divisi,
                    COUNT(DISTINCT CASE WHEN u.level NOT IN ('Admin Instansi', 'SuperAdmin', 'admin_instansi', 'superadmin') THEN u.id END) AS total_member,
                    COUNT(DISTINCT u.id) AS total_all_users,
                    COUNT(DISTINCT CASE WHEN k.tgl IS NOT NULL AND strftime('%Y', k.tgl) = strftime('%Y', 'now') THEN k.id_kesehatan END) AS total_kesehatan_tahun_ini,
                    COUNT(DISTINCT CASE WHEN h.tgl IS NOT NULL AND strftime('%Y', h.tgl) = strftime('%Y', 'now') THEN h.id_hb END) AS total_hb_tahun_ini,
                    COUNT(DISTINCT CASE WHEN h.status = 'Anemia' AND strftime('%Y', h.tgl) = strftime('%Y', 'now') THEN h.id_hb END) AS total_anemia_tahun_ini,
                    COUNT(DISTINCT CASE WHEN k.status = 'Normal' AND strftime('%Y', k.tgl) = strftime('%Y', 'now') THEN k.id_kesehatan END) AS total_normal_imt_tahun_ini,
                    COUNT(DISTINCT CASE WHEN k.status_darah LIKE 'Hipertensi%' AND strftime('%Y', k.tgl) = strftime('%Y', 'now') THEN k.id_kesehatan END) AS total_hipertensi_tahun_ini
                FROM instansi i
                LEFT JOIN divisi d ON d.instansi_id = i.id
                LEFT JOIN users u ON u.instansi_id = i.id
                LEFT JOIN kesehatan k ON k.instansi_id = i.id
                LEFT JOIN hb h ON h.instansi_id = i.id
                GROUP BY i.id, i.nama_instansi, i.jenis_instansi;
            ");
        } else {
            DB::statement("
                CREATE OR REPLACE VIEW v_instansi_health_summary AS
                SELECT 
                    i.id AS instansi_id,
                    i.nama_instansi,
                    i.jenis_instansi,
                    COUNT(DISTINCT d.id) AS total_divisi,
                    COUNT(DISTINCT CASE WHEN u.level NOT IN ('Admin Instansi', 'SuperAdmin', 'admin_instansi', 'superadmin') THEN u.id END) AS total_member,
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('DROP VIEW IF EXISTS v_instansi_health_summary;');
            DB::statement("
                CREATE VIEW v_instansi_health_summary AS
                SELECT 
                    i.id AS instansi_id,
                    i.nama_instansi,
                    i.jenis_instansi,
                    COUNT(DISTINCT d.id) AS total_divisi,
                    COUNT(DISTINCT CASE WHEN u.level = 'Member' THEN u.id END) AS total_member,
                    COUNT(DISTINCT u.id) AS total_all_users,
                    COUNT(DISTINCT CASE WHEN k.tgl IS NOT NULL AND strftime('%Y', k.tgl) = strftime('%Y', 'now') THEN k.id_kesehatan END) AS total_kesehatan_tahun_ini,
                    COUNT(DISTINCT CASE WHEN h.tgl IS NOT NULL AND strftime('%Y', h.tgl) = strftime('%Y', 'now') THEN h.id_hb END) AS total_hb_tahun_ini,
                    COUNT(DISTINCT CASE WHEN h.status = 'Anemia' AND strftime('%Y', h.tgl) = strftime('%Y', 'now') THEN h.id_hb END) AS total_anemia_tahun_ini,
                    COUNT(DISTINCT CASE WHEN k.status = 'Normal' AND strftime('%Y', k.tgl) = strftime('%Y', 'now') THEN k.id_kesehatan END) AS total_normal_imt_tahun_ini,
                    COUNT(DISTINCT CASE WHEN k.status_darah LIKE 'Hipertensi%' AND strftime('%Y', k.tgl) = strftime('%Y', 'now') THEN k.id_kesehatan END) AS total_hipertensi_tahun_ini
                FROM instansi i
                LEFT JOIN divisi d ON d.instansi_id = i.id
                LEFT JOIN users u ON u.instansi_id = i.id
                LEFT JOIN kesehatan k ON k.instansi_id = i.id
                LEFT JOIN hb h ON h.instansi_id = i.id
                GROUP BY i.id, i.nama_instansi, i.jenis_instansi;
            ");
        } else {
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
        }
    }
};

