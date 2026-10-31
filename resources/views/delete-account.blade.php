<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penghapusan Akun & Data Pengguna | Aku Sehat</title>
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('src/aku_sehat_icon.png') }}" alt="Aku Sehat" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform">
                <div class="flex flex-col">
                    <span class="font-bold text-slate-900 text-base leading-tight">Aku Sehat</span>
                    <span class="text-[10px] text-emerald-800 font-medium">Neura Cakrawira Asia</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('privacy.policy') }}" class="text-xs font-semibold text-slate-600 hover:text-emerald-800 transition-colors">
                    Kebijakan Privasi
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-white bg-[#308a34] hover:bg-[#1B5E20] transition-colors shadow-sm">
                    <span>Masuk</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 w-full">
        <div class="mb-8 border-b border-slate-200 pb-6 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200 mb-3">
                <i class="fas fa-user-xmark"></i>
                <span>Permintaan Penghapusan Akun & Data</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Penghapusan Akun & Retensi Data Pengguna
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">
                Aplikasi <strong>Aku Sehat</strong> dikelola dan dikembangkan oleh <strong>Neura Cakrawira Asia</strong>.
            </p>
        </div>

        <div class="space-y-8 text-sm leading-relaxed text-slate-700">
            <!-- Informasi Pengembang -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 space-y-2">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-shield-halved text-emerald-700"></i>
                    Komitmen Perlindungan Privasi Anda
                </h2>
                <p class="text-xs sm:text-sm text-slate-600">
                    Sesuai dengan Kebijakan Privasi dan persyaratan Google Play, pengguna aplikasi <strong>Aku Sehat</strong> memiliki hak penuh untuk meminta penghapusan akun mereka beserta seluruh data pribadi yang tersimpan di sistem kami kapan saja.
                </p>
            </div>

            <!-- Langkah-langkah Penghapusan Akun -->
            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">1</span>
                    Langkah-langkah Meminta Penghapusan Akun
                </h2>

                <div class="grid grid-cols-1 gap-3">
                    <div class="p-4 bg-white rounded-xl border border-slate-200 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            A
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Pengajuan via Email Resmi (Direkomendasikan)</h3>
                            <p class="text-xs text-slate-600 mt-1">
                                Kirimkan pesan permohonan penghapusan akun dari alamat email yang terdaftar ke email pengembang kami:
                            </p>
                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 font-mono text-xs text-slate-800 mt-2 space-y-1">
                                <p><strong>Kepada:</strong> <a href="mailto:neuracakrawira@yahoo.com" class="text-emerald-800 underline">neuracakrawira@yahoo.com</a></p>
                                <p><strong>Subjek:</strong> Permintaan Penghapusan Akun Aku Sehat - [Username Anda]</p>
                                <p><strong>Isi Pesan:</strong> Nama lengkap, Username, Instansi/Sekolah (jika ada), dan alasan penghapusan (opsional).</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                            B
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Pengajuan Melalui Hubungi Kami / Administrator Instansi</h3>
                            <p class="text-xs text-slate-600 mt-1">
                                Jika akun Anda dibuat oleh Administrator Sekolah/Instansi Anda, Anda juga dapat menghubungi Administrator instansi terkait atau menghubungi WhatsApp/Telepon layanan kami di <a href="tel:+62895383107479" class="text-emerald-800 font-bold hover:underline">+62 895-3831-07479</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Jenis Data yang Dihapus -->
            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">2</span>
                    Jenis Data yang Dihapus Permanen
                </h2>
                <div class="bg-white p-5 rounded-xl border border-slate-200 space-y-2">
                    <p class="text-xs sm:text-sm text-slate-600">
                        Begitu permohonan penghapusan akun Anda diproses dan diverifikasi, data berikut akan <strong>dihapus permanen</strong> dari server kami:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-700 pl-2">
                        <li><strong>Data Akun & Profil Pribadi:</strong> Nama lengkap, nama pengguna (username), kata sandi terenkripsi, alamat email, nomor telepon, tanggal lahir/usia, dan jenis kelamin.</li>
                        <li><strong>Data Rekam Kesehatan & Fisik:</strong> Catatan tinggi badan, berat badan, indeks massa tubuh (BMI), dan riwayat pemeriksaan kadar Hemoglobin (Hb).</li>
                        <li><strong>Data Aktivitas Olahraga & Pelacakan:</strong> Riwayat catatan lari (tracking run), log kebugaran, dan catatan hidrasi harian.</li>
                        <li><strong>Token & Sesi Perangkat:</strong> Seluruh token autentikasi (Personal Access Tokens) yang tersimpan di perangkat mobile.</li>
                    </ul>
                </div>
            </section>

            <!-- Data yang Disimpan & Periode Retensi -->
            <section class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">3</span>
                    Data yang Disimpan & Periode Retensi Tambahan
                </h2>
                <div class="bg-white p-5 rounded-xl border border-slate-200 space-y-3 text-xs sm:text-sm text-slate-700">
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Catatan Keuangan & Billing (Jika Pernah Transaksi):</h3>
                        <p class="text-slate-600">
                            Untuk transaksi perpanjangan layanan instansi atau pembelian kuota melalui Google Payment / Google Play Billing, data invoice (ID pesanan, nominal, dan tanggal transaksi) disimpan hingga <strong>5 tahun</strong> semata-mata untuk mematuhi kewajiban pembukuan keuangan dan undang-undang perpajakan yang berlaku di Indonesia. Data ini tidak digunakan untuk tujuan operasional atau pemasaran.
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <h3 class="font-bold text-slate-900 mb-1">Periode Waktu Pemrosesan:</h3>
                        <p class="text-slate-600">
                            Permintaan penghapusan akun akan diverifikasi dan diselesaikan secara penuh dalam jangka waktu maksimal <strong>7 hingga 14 hari kerja</strong> sejak permohonan diterima. Kami akan mengirimkan email konfirmasi apabila akun telah berhasil dihapus secara permanen.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Kontak Bantuan -->
            <div class="p-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl space-y-2">
                <h3 class="font-bold text-sm text-emerald-400">Pusat Bantuan & Layanan Privasi Pengguna</h3>
                <p class="text-xs text-slate-300">
                    Jika Anda memiliki pertanyaan mengenai kebijakan penghapusan akun atau ingin menanyakan status data Anda, silakan hubungi kami:
                </p>
                <div class="text-xs font-mono space-y-0.5 text-emerald-200 pt-1">
                    <p><strong class="font-sans text-white">Neura Cakrawira Asia</strong></p>
                    <p>Email: <a href="mailto:neuracakrawira@yahoo.com" class="underline text-emerald-300">neuracakrawira@yahoo.com</a></p>
                    <p>Telepon: +62 895-3831-07479</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-5xl mx-auto px-4">
            <p>Aku Sehat &copy; {{ date('Y') }} &bull; Neura Cakrawira Asia. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>
