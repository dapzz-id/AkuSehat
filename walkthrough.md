# Daftar Fitur Backend Satu Sekolah

Backend "Satu Sekolah" dirancang sebagai sistem ERP terpadu untuk sekolah (Multi-Tenant/SaaS), yang mencakup fungsionalitas akademik, administrasi, hingga operasional harian sekolah. Berikut adalah penjabaran fitur-fitur yang sudah terimplementasi beserta fungsinya:

## 1. Manajemen Inti & Hak Akses (Core & RBAC)
Fitur ini menjadi pondasi seluruh aplikasi. 
- **Manajemen Tenant (Sekolah)**: Memungkinkan aplikasi digunakan oleh banyak sekolah secara bersamaan (SaaS) tanpa saling tumpang tindih data.
- **RBAC (Role-Based Access Control)**: Pengaturan role fleksibel seperti `Admin`, `Staff`, `Student`, dan `Parent`. 
- **Manajemen Akun & Autentikasi**: Sistem login, profil pengguna, dan keamanan otorisasi berbasis JWT.

## 2. Akademik, Pembelajaran & Penilaian (LMS & Raport)
Sistem manajemen pembelajaran digital untuk mendukung KBM:
- **Manajemen Kursus & Modul**: Guru dapat membuat mata pelajaran dan membagikan materi (modul) ke siswa.
- **Pengumpulan Tugas**: Siswa dapat mengupload hasil pekerjaan/tugas secara digital untuk dinilai guru.
- **Computer Based Assessment (CBA)**: Ujian online mandiri dengan dukungan soal kaya media (gambar/audio/video) dan pencocokan (tarik garis).
- **Raport Digital & Leaderboard**: Fitur yang baru ditambahkan untuk meng-generate Excel template nilai, proses upload massal oleh wali kelas, serta perankingan (leaderboard) instan berdasarkan kelas, jurusan, maupun angkatan.

## 3. Keuangan & Dompet Digital (Web3 Ledger)
Backend ini memiliki sistem Ledger (buku besar) internal bergaya *crypto-wallet* yang immutable (tidak bisa diubah sembarangan).
- **Dompet Digital Siswa**: Memungkinkan siswa dan staf memiliki saldo digital yang diamankan menggunakan transaksi dengan hashing (*previous hash & current hash*).
- **Tagihan & Pembayaran (Billing)**: Admin dapat membuat tagihan (seperti SPP, uang gedung, dll) dan siswa/wali dapat langsung membayarnya dari saldo Ledger menggunakan PIN Hash.
- **Pembelian Token AI via Midtrans**: Integrasi webhook untuk pembelian paket kuota AI bagi sekolah secara otomatis.

## 4. Kantin Digital (Digital Canteen & POS)
Mendigitalisasi transaksi di lingkungan sekolah agar *cashless*.
- **Pendaftaran Tenant Kantin**: Pemilik toko/kantin dapat membuka lapak dan menambah menu.
- **Sistem POS & QR Static**: Pembeli (Siswa/Guru) bisa membayar langsung ke penjual dengan *scan QR* kantin dan memasukkan PIN dompet digital.
- **Pre-order Keranjang**: Siswa bisa memesan makanan lebih dulu dan mengambilnya saat istirahat (checkout keranjang).

## 5. Administrasi & Kesiswaan
Otomatisasi izin dan pendisiplinan murid.
- **Gate Pass (Surat Izin Keluar)**: Siswa mengajukan izin keluar sekolah, yang akan melewati *approval* (persetujuan) bertingkat (misal: Guru -> Kesiswaan). Proses keluar dan kembali diverifikasi menggunakan pemindaian QR oleh Satpam.
- **Sistem Poin Pelanggaran (Violations)**: Mencatat pelanggaran siswa/guru (termasuk upload bukti foto) dan secara otomatis mengkalkulasi poin hukuman. Jika terlambat absen, sistem juga menginjeksi poin telat secara otomatis.

## 6. Kesehatan & UKS (Unit Kesehatan Sekolah)
Modul untuk memantau kesehatan warga sekolah.
- **Rekam Medis**: Pencatatan riwayat kesehatan rutin (TB, BB, HB, Kesehatan Mata/Gigi) yang juga bisa dianalisa oleh sistem AI.
- **Siklus Haid & Peminjaman Pita**: Siswi dapat memantau siklus haid, serta meminjam pita tanda haid ke petugas UKS. UKS memonitor siswi yang "overdue" (telat mengembalikan pita lebih dari 7 hari) untuk diberikan sanksi atau pemanggilan.

## 7. Kepegawaian & Kinerja (Penilaian Kinerja Guru/PKG)
- **Manajemen Evaluasi Staff**: Guru dapat mengupload dokumen portofolio kerjanya (seperti RPP, Piagam), lalu admin akan mengevaluasi dan memberikan skor berdasar indikator periode berjalan.
- **Fair Average Calculation**: Kalkulasi performa yang adil untuk staf sesuai indikator yang hanya berkaitan dengan tupoksinya.

## 8. Penerimaan Murid Baru (SPMB)
- Manajemen pendaftaran siswa baru secara digital, mulai dari formulir *online*, persetujuan berkas, penjadwalan tes masuk, hingga *re-register* (daftar ulang) jika dinyatakan diterima.

## 9. Inventaris & Fasilitas Lainnya
- **Perpustakaan Digital**: Katalog buku fisik maupun e-book digital, pengaturan peminjaman, serta akses jurnal riset untuk siswa.
- **Absensi (IoT & Face Rekognition)**: Catatan presensi siswa terintegrasi ke sistem dan mesin absensi fisik / API cloud.
- **Bursa Kerja & PKL**: SMK dapat menyediakan lowongan kerja/PKL. Siswa (Student) juga bisa membuat Portofolio *(ala LinkedIn)* untuk *showcase* karya mereka.
- **Komunikasi**: Penyiaran (Broadcast) pengumuman / mading digital ke audiens yang ditargetkan (contoh: hanya kelas tertentu atau semua warga sekolah).
