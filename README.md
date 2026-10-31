# 🏥 Aku Sehat — Backend & Web Application (V1)

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

Platform manajemen dan pemantauan kesehatan terpadu multi-instansi (**Aku Sehat**) berbasis arsitektur modular (*domain-driven*). Dirancang untuk mendukung operasional UKS sekolah, institusi pendidikan, dan korporat dengan pemisahan akses berbasis peran (*Role-Based Access Control*), enkripsi data kesehatan sensitif (*tenant encryption*), serta penyediaan API berperforma tinggi untuk aplikasi mobile.

---

## 🚀 Fitur Unggulan

### 1. Arsitektur Domain Modular (`app/Modules/`)
- **User & Auth**: Manajemen autentikasi web & API token (Laravel Sanctum), Google Sign-In simulasi/integrasi, serta registrasi mandiri via kode undangan instansi.
- **Kesehatan & Klinis**: Pencatatan skrining antropometri (TB, BB, IMT otomatis), pemeriksaan fisik (telinga, mata, gigi), perilaku berisiko, dan kesehatan reproduksi disertai ekspor laporan PDF formal.
- **Hemoglobin (Hb)**: Pemantauan kadar Hb berkala, klasifikasi anemia, tren riwayat, dan ekspor data klinis.
- **Kesehatan Remaja & Menstruasi (Mens)**: Rekapitulasi siklus haid, pencatatan gejala/keluhan nyeri haid, dan riwayat kesehatan harian.
- **Divisi & Unit Kerja**: Pengelompokan anggota berdasarkan kelas/divisi dengan kustomisasi warna cover divisi (*tertiary color*) yang diwariskan ke aplikasi mobile.
- **SuperAdmin & Multi-Tenancy**: Pengelolaan lisensi software (*license keys*), konfigurasi modul per-instansi, status pemeliharaan sistem (*maintenance mode* dengan *bypass key*), serta audit log *append-only* (tidak dapat dimanipulasi).
- **Billing & Transaksi**: Manajemen pembayaran langganan instansi dengan integrasi Midtrans dan pencatatan transaksi terverifikasi.

### 2. Multi-Theme Engine Web
Dukungan dual-theme pada dashboard web dengan deteksi preferensi dinamis:
- **Emerald Classic**: Tema medis klinis bersih dengan nuansa hijau emerald.
- **Atlas Neo Modern**: Tema modern futuristik bergaya glassmorphism dengan aksen telemetry cards.

### 3. Keamanan & Kepatuhan Standar Industri
- **Enkripsi Data Sensitif**: Field medis rahasia dienkripsi secara deterministik/tenant-specific (`SafeEncrypted` & `TenantEncrypted`).
- **Audit Logging Imutabel**: Riwayat mutasi data penting dicatat pada tabel audit log *append-only*.
- **Google Play Compliance**: Halaman publik Kebijakan Privasi (`/kebijakan-privasi`) dan Penghapusan Akun/Data Mandiri (`/hapus-akun`).
- **Security Headers Middleware**: Perlindungan XSS, CSRF, Clickjacking (X-Frame-Options), dan Rate Limiting bawaan.

---

## 👥 Hak Akses Pengguna (RBAC)

| Peran | Ruang Lingkup | Deskripsi Akses |
|---|---|---|
| **Super Admin** | Global / Multi-Instansi | Mengatur lisensi instansi, konfigurasi modul, maintenance, terminal monitoring, dan audit trail |
| **Admin Instansi** | Instansi Terkait | Mengelola data divisi, pengguna, rekap klinis seluruh anggota, dan pengaturan modul instansi |
| **Health Consultant** | Divisi / Instansi | Konseling kesehatan, pemantauan riwayat klinis anggota, dan rekomendasi tindak lanjut |
| **Health Monitor** | Divisi / UKS | Peminjaman pita/fasilitas UKS, pencatatan denda/keterlambatan, dan peringatan kesehatan |
| **Member** | Pribadi | Melihat rekapitulasi kesehatan pribadi, riwayat Hb, siklus haid, dan konsultasi |

---

## 🛠️ Persyaratan Sistem

- **PHP**: `>= 8.3` (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd` / `imagick`, `bcmath`, `fileinfo`)
- **Composer**: `>= 2.5`
- **Node.js**: `>= 20.x` & **NPM**: `>= 10.x`
- **Database**: MySQL `>= 8.0` atau MariaDB `>= 10.5`

---

## ⚙️ Panduan Instalasi

### 1. Kloning Repository & Checkout Branch
```bash
git clone https://github.com/dapzz-id/AkuSehat.git
cd AkuSehat
git checkout Website_V1
```

### 2. Pasang Dependensi Backend & Frontend
```bash
# Dependensi PHP
composer install

# Dependensi JavaScript
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi contoh dan sesuaikan parameter database serta kunci aplikasi:
```bash
cp .env.example .env
php artisan key:generate
```

Pastikan pengaturan koneksi database pada `.env` telah sesuai:
```env
APP_NAME="Aku Sehat"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=akusehat_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Database & Seeder
Jalankan migrasi tabel beserta data awal bawaan:
```bash
php artisan migrate --seed
```

### 5. Kompilasi Aset Frontend
```bash
# Mode pengembangan (HMR aktif)
npm run dev

# ATAU kompilasi produksi
npm run build
```

### 6. Menjalankan Server Lokal
```bash
# Menjalankan seluruh layanan (Artisan server, Queue listener, Pail logs, Vite) sekaligus:
composer run dev

# Atau server artisan mandiri:
php artisan serve
```

Aplikasi web dapat diakses melalui browser di `http://localhost:8000`.

---

## 🔑 Kredensial Akun Default (Hasil Seeder)

| Peran | Username / Email | Password |
|---|---|---|
| **Super Admin** | `superadmin@akusehat.id` / `superadmin` | `password123` |
| **Admin Instansi** | `admin` | `password123` |
| **Health Consultant** | `healthconsultant` | `password123` |
| **Health Monitor** | `healthmonitor` | `password123` |
| **Member** | `member1` s/d `member50` | `password123` |

---

## 📁 Struktur Direktori Utama

```text
Website_L13/
├── app/
│   ├── Casts/                  # SafeEncrypted & TenantEncrypted data casts
│   ├── Enums/                  # AuditCategory, BillingStatus, dsb.
│   ├── Http/Middleware/        # TenantAccess, SecurityHeaders, RoleMiddleware
│   ├── Models/                 # Eloquent Models (User, Kesehatan, Hb, AuditLog)
│   ├── Modules/                # Modul Fitur Domain
│   │   ├── Billing/            # Langganan & integrasi pembayaran
│   │   ├── Divisi/             # Manajemen unit divisi & perizinan modul
│   │   ├── Hb/                 # Skrining & laporan hemoglobin
│   │   ├── Kesehatan/          # Pemeriksaan fisik antropometri & klinis
│   │   ├── Mens/               # Pemantauan siklus haid
│   │   ├── SuperAdmin/         # Lisensi, terminal, audit, maintenance
│   │   └── User/               # User management, member dashboard
│   └── Services/               # AuditLogger, TenantEncryptionService
├── bootstrap/app.php           # Routing & Middleware bootstrap Laravel 13
├── config/                     # Konfigurasi aplikasi, auth, session, database
├── database/
│   ├── migrations/             # Migrasi database terstruktur
│   └── seeders/                # Database Seeder & Mock Data
├── public/                     # Web root publik
├── resources/
│   ├── css/                    # Tailwind CSS stylesheets
│   └── views/                  # Blade templates (Dashboard, SuperAdmin, Admin)
└── routes/
    ├── api.php                 # Endpoint API Sanctum untuk Mobile
    └── web.php                 # Route aplikasi web
```

---

## 🔒 Lisensi & Hak Cipta

Hak Cipta © 2026 **Aku Sehat**. Seluruh hak cipta dilindungi undang-undang.
Penggunaan backend dan lisensi komersial diatur berdasarkan perjanjian kemitraan resmi.
