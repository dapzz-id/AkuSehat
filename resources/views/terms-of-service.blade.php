<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persyaratan Layanan | Aku Sehat</title>
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- Top Navigation Header -->
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
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#308a34] hover:bg-[#1B5E20] transition-colors shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 w-full">
        <!-- Title & Metadata -->
        <div class="mb-10 text-center sm:text-left border-b border-slate-200 pb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-4">
                <i class="fas fa-file-contract"></i>
                <span>Ketentuan Hukum & Perjanjian Pengguna</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Persyaratan Layanan</h1>
            <p class="text-slate-500 text-sm mt-1">Terms of Service &mdash; Aplikasi Mobile & Platform Web Aku Sehat</p>
            <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                <span><i class="far fa-calendar-check mr-1 text-emerald-700"></i> Tanggal berlaku: <strong>1 Januari 2026</strong></span>
                <span><i class="far fa-clock mr-1 text-emerald-700"></i> Terakhir diperbarui: <strong>5 November 2026</strong></span>
            </div>
        </div>

        <!-- Terms Content Article -->
        <article class="prose prose-slate max-w-none text-sm sm:text-base leading-relaxed space-y-8 text-slate-700">
            <div class="p-4 sm:p-5 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl text-emerald-950 text-sm">
                <p>
                    Selamat datang di <strong>Aku Sehat</strong>. Persyaratan Layanan ini merupakan perjanjian yang mengikat secara hukum antara Anda (pengguna, siswa, guru, staf, atau institusi mitra) dengan <strong>Neura Cakrawira Asia</strong> ("kami", "pengembang") selaku penyedia aplikasi mobile <strong>Aku Sehat</strong> dan platform web 
                    <a href="https://akusehat.neuracakrawira.asia/" target="_blank" class="font-bold underline text-emerald-800 hover:text-emerald-900">akusehat.neuracakrawira.asia</a>.
                </p>
                <p class="mt-2 text-xs text-emerald-900/80">
                    Dengan mengunduh, menginstal, mendaftar, atau menggunakan Layanan kami, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui seluruh ketentuan di dalam dokumen ini.
                </p>
            </div>

            <!-- 1. Ruang Lingkup Layanan -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">1</span>
                    Ruang Lingkup & Deskripsi Layanan
                </h2>
                <p>
                    <strong>Aku Sehat</strong> adalah ekosistem digital manajemen kesehatan terpadu berbasis sekolah dan institusi. Layanan kami mencakup modul pemantauan rekam medis fisik, pencatatan hemoglobin (skrining anemia), siklus kesehatan reproduksi siswi (FemHealth), pelacak aktivitas fisik & lari (Run Tracker), pemantauan vitalitas & tidur, serta fitur bimbingan konseling SafeSpace yang menghubungkan pengguna dengan konselor institusi.
                </p>
            </section>

            <!-- 2. Batasan Tanggung Jawab Medis (Medical Disclaimer) -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">2</span>
                    Batasan Medis (Bukan Pengganti Nasihat Dokter)
                </h2>
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs sm:text-sm space-y-2">
                    <p class="font-semibold flex items-center gap-1.5 text-amber-800">
                        <i class="fas fa-triangle-exclamation"></i>
                        DISCLAIMER KESEHATAN PENTING:
                    </p>
                    <p>
                        Aplikasi Aku Sehat berfungsi semata-mata sebagai sarana pencatatan administrasi, edukasi, dan pemantauan preventif lingkungan sekolah. Layanan ini <strong>BUKAN</strong> merupakan fasilitas diagnosis medis darurat atau pengganti konsultasi dengan dokter profesional berlisensi.
                    </p>
                    <p>
                        Apabila Anda atau siswa mengalami kondisi darurat medis, segera hubungi fasilitas layanan kesehatan primer (puskesmas, klinik, atau rumah sakit terdekat).
                    </p>
                </div>
            </section>

            <!-- 3. Akun Pengguna & Keamanan -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">3</span>
                    Akun Pengguna, Kredensial & Kewajiban
                </h2>
                <p>
                    Dalam penggunaan Layanan, Anda wajib menjaga kerahasiaan username, password, dan token akses akun Anda. Anda bertanggung jawab penuh atas seluruh aktivitas yang terjadi di bawah akun Anda.
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-xs sm:text-sm text-slate-600">
                    <li>Pengguna dilarang meminjamkan kredensial login kepada pihak lain tanpa hak.</li>
                    <li>Pengguna dilarang melakukan manipulasi data, injeksi kode jahat, reverse engineering, atau upaya eksploitasi sistem.</li>
                    <li>Kami berhak menangguhkan atau menghentikan akses akun yang terbukti melanggar norma hukum, etika sekolah, atau membahayakan keamanan integritas sistem.</li>
                </ul>
            </section>

            <!-- 4. Perlindungan Data & Privasi -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">4</span>
                    Perlindungan Data & Privasi
                </h2>
                <p>
                    Privasi dan keamanan data rekam kesehatan Anda dijamin dengan standar enkripsi modern. Ketentuan detail mengenai tata cara pengumpulan, pemrosesan, dan penyimpanan data pribadi diatur secara terpisah dalam dokumen 
                    <a href="{{ url('/kebijakan-privasi') }}" class="font-bold underline text-emerald-800 hover:text-emerald-900">Kebijakan Privasi Aku Sehat</a>.
                </p>
            </section>

            <!-- 5. Hak Kekayaan Intelektual -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">5</span>
                    Hak Kekayaan Intelektual
                </h2>
                <p>
                    Seluruh desain grafis, antarmuka pengguna, kode program, logo, merek dagang, dan aset digital Aku Sehat merupakan hak cipta eksklusif milik <strong>Neura Cakrawira Asia</strong> dan dilindungi oleh Undang-Undang Hak Cipta Republik Indonesia serta regulasi internasional.
                </p>
            </section>

            <!-- 6. Perubahan Persyaratan -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">6</span>
                    Perubahan Ketentuan (Update Terms)
                </h2>
                <p>
                    Kami dapat memperbarui Persyaratan Layanan ini sewaktu-waktu seiring penyempurnaan fitur dan pemenuhan regulasi hukum. Versi terbaru akan selalu dipublikasikan secara terbuka di tautan ini. Kelanjutan penggunaan Layanan setelah pembaruan menandakan persetujuan Anda atas ketentuan yang diperbarui.
                </p>
            </section>

            <!-- 7. Kontak & Bantuan Hukum -->
            <section class="space-y-4 pt-4 border-t border-slate-200">
                <div class="p-5 bg-gradient-to-br from-emerald-900 to-slate-900 text-white rounded-2xl shadow-sm">
                    <h3 class="font-bold text-base mb-2">Informasi Kontak & Entitas Pengembang</h3>
                    <p class="text-xs text-emerald-100/90 mb-3">Jika Anda memiliki pertanyaan mengenai Persyaratan Layanan ini, silakan hubungi tim kami:</p>
                    <div class="text-xs space-y-1 font-mono text-emerald-200">
                        <p><strong class="font-sans text-white">Neura Cakrawira Asia</strong></p>
                        <p>Pengembang Resmi Aplikasi: Aku Sehat</p>
                        <p>Email: <a href="mailto:neuracakrawira@yahoo.com" class="underline text-emerald-300">neuracakrawira@yahoo.com</a></p>
                        <p>Telepon / WhatsApp: +62 895-3831-07479</p>
                        <p>Website: <a href="https://akusehat.neuracakrawira.asia" class="underline text-emerald-300" target="_blank">https://akusehat.neuracakrawira.asia</a></p>
                    </div>
                </div>
            </section>
        </article>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-5xl mx-auto px-4 space-y-1">
            <div class="flex items-center justify-center gap-4 mb-2 text-slate-400">
                <a href="{{ url('/kebijakan-privasi') }}" class="hover:text-emerald-700 transition-colors">Kebijakan Privasi</a>
                <span>&bull;</span>
                <a href="{{ url('/syarat-ketentuan') }}" class="hover:text-emerald-700 font-semibold text-slate-600 transition-colors">Persyaratan Layanan</a>
                <span>&bull;</span>
                <a href="{{ url('/hapus-akun') }}" class="hover:text-emerald-700 transition-colors">Hapus Akun</a>
            </div>
            <p>Aku Sehat &copy; 2026 &bull; Dikelola oleh Neura Cakrawira Asia. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>
