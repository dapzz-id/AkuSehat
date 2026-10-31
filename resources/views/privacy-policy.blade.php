<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi | Aku Sehat</title>
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
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#308a34] hover:bg-[#1B5E20] transition-colors shadow-sm">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk ke Akun</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 w-full">
        <!-- Title & Metadata -->
        <div class="mb-10 text-center sm:text-left border-b border-slate-200 pb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-4">
                <i class="fas fa-shield-halved"></i>
                <span>Dokumen Resmi Hukum & Perlindungan Data</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Kebijakan Privasi</h1>
            <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                <span><i class="far fa-calendar-check mr-1 text-emerald-700"></i> Tanggal berlaku: <strong>21 Oktober 2026</strong></span>
                <span><i class="far fa-clock mr-1 text-emerald-700"></i> Terakhir diperbarui: <strong>21 Oktober 2026</strong></span>
            </div>
        </div>

        <!-- Privacy Content Article -->
        <article class="prose prose-slate max-w-none text-sm sm:text-base leading-relaxed space-y-8 text-slate-700">
            <div class="p-4 sm:p-5 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl text-emerald-950 text-sm">
                <p>
                    Kebijakan Privasi ini menjelaskan bagaimana <strong>Neura Cakrawira Asia</strong> (“kami”) mengumpulkan, menggunakan, dan membagikan data pribadi saat Anda mengunjungi 
                    <a href="https://akusehat.neuracakrawira.asia/" target="_blank" class="font-bold underline text-emerald-800 hover:text-emerald-900">akusehat.neuracakrawira.asia</a> atau menggunakan aplikasi <strong>Aku Sehat</strong> (bersama-sama disebut “Layanan”), serta pilihan dan hak yang Anda miliki.
                </p>
            </div>

            <!-- Siapa Kami -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">1</span>
                    Siapa Kami
                </h2>
                <p>
                    Layanan ini dikelola oleh <strong>Neura Cakrawira Asia</strong>, seorang individu. Kami berdomisili di Indonesia. Kami bertanggung jawab atas data pribadi Anda. Undang-undang pelindungan data menyebut peran ini sebagai “pengendali data pribadi”.
                </p>
                <div class="bg-white p-4 rounded-xl border border-slate-200 space-y-1 text-xs sm:text-sm">
                    <p><strong>Email:</strong> <a href="mailto:neuracakrawira@yahoo.com" class="text-emerald-800 hover:underline">neuracakrawira@yahoo.com</a></p>
                    <p><strong>Alamat surat:</strong> <span class="text-slate-600">neuracakrawira@yahoo.com</span></p>
                    <p><strong>Telepon:</strong> <a href="tel:+62895383107479" class="text-emerald-800 hover:underline">+62 895-3831-07479</a></p>
                </div>
            </section>

            <!-- Informasi yang kami kumpulkan -->
            <section class="space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">2</span>
                    Informasi yang Kami Kumpulkan
                </h2>
                
                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Informasi yang Anda berikan kepada kami:</h3>
                    <ul class="list-disc list-inside space-y-1 pl-2 text-slate-600 text-sm">
                        <li><strong>Saat Anda menghubungi kami:</strong> nama, alamat email, isi pesan Anda.</li>
                        <li><strong>Saat Anda membuat akun:</strong> nama, alamat email, nama pengguna, kata sandi, nomor telepon, tanggal lahir atau usia.</li>
                        <li><strong>Saat Anda melakukan pemesanan:</strong> nama, alamat email, detail pembayaran, detail dan riwayat pesanan.</li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Informasi yang dikumpulkan secara otomatis:</h3>
                    <p class="text-sm text-slate-600">
                        <strong>Log server</strong>, disimpan untuk menjalankan Layanan dan menjaga keamanannya: alamat IP, jenis perangkat dan browser, tanggal dan waktu akses.
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Informasi yang dapat diakses aplikasi dengan izin Anda:</h3>
                    <p class="text-sm text-slate-600">
                        Aplikasi hanya meminta izin berikut saat suatu fitur membutuhkannya. Anda dapat mencabut izin kapan saja di pengaturan perangkat Anda:
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs font-medium text-slate-700">
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center gap-2">
                            <i class="fas fa-camera text-emerald-700"></i> Kamera
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center gap-2">
                            <i class="fas fa-images text-emerald-700"></i> Foto dan media
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center gap-2">
                            <i class="fas fa-bell text-emerald-700"></i> Notifikasi
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center gap-2">
                            <i class="fas fa-location-dot text-emerald-700"></i> Perkiraan lokasi
                        </div>
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200 flex items-center gap-2 sm:col-span-2">
                            <i class="fas fa-crosshairs text-emerald-700"></i> Lokasi presisi
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 bg-slate-100 p-3 rounded-xl mt-2">
                        <strong>Kegunaannya bagi kami:</strong> Lokasi untuk fitur tracking run. Kamera, Foto, dan Media untuk mengunggah dengan yang berkaitan dengan media, seperti foto profile, dll.
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="text-base font-bold text-slate-900">Informasi dari sumber lain:</h3>
                    <p class="text-sm text-slate-600">
                        Jika Anda masuk dengan akun dari penyedia lain (Sign in with Google), penyedia tersebut mengirimkan detail profil dasar kepada kami, seperti nama dan alamat email Anda.
                    </p>
                    <p class="text-sm text-slate-600">
                        Penyedia pembayaran kami memberi tahu kami apakah suatu pembayaran berhasil. Kami tidak menerima atau menyimpan nomor kartu lengkap Anda.
                    </p>
                </div>
            </section>

            <!-- Cara kami menggunakan informasi Anda -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">3</span>
                    Cara Kami Menggunakan Informasi Anda
                </h2>
                <p>Kami menggunakan data pribadi hanya untuk tujuan di bawah ini. Jika hukum mensyaratkan dasar hukum, kami mengandalkan dasar yang ditunjukkan untuk setiap tujuan:</p>
                <div class="space-y-2">
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Menjawab pertanyaan dan permintaan Anda.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: kepentingan sah kami (membalas orang yang menghubungi kami).</span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Membuat dan mengelola akun Anda.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: pelaksanaan kontrak kami dengan Anda.</span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Memproses pesanan, pembayaran, pengiriman, dan pengembalian dana.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: pelaksanaan kontrak kami dengan Anda.</span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Menyimpan catatan yang diwajibkan oleh undang-undang perpajakan dan akuntansi.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: kewajiban hukum.</span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Menjaga keamanan Layanan, mencegah penyalahgunaan, dan memperbaiki kesalahan.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: kepentingan sah kami (menjalankan layanan yang aman dan andal).</span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Menyediakan fitur aplikasi yang memerlukan izin perangkat.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: persetujuan Anda.</span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <strong class="text-slate-900">Menyediakan fitur berbasis AI.</strong>
                        <span class="text-slate-500 block sm:inline sm:ml-2">Dasar hukum: pelaksanaan kontrak kami dengan Anda.</span>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-slate-600">
                    Jika kami mengandalkan persetujuan Anda, Anda dapat menarik persetujuan itu kapan saja. Hal ini tidak memengaruhi apa yang telah kami lakukan sebelum persetujuan ditarik.
                </p>
                <p class="text-xs sm:text-sm text-slate-600">
                    Tidak ada undang-undang yang mewajibkan Anda memberikan data pribadi kepada kami, tetapi sebagian Layanan tidak dapat berfungsi tanpa data tersebut. Kami memerlukan detail tertentu untuk menyelesaikan pesanan. Ini merupakan persyaratan kontrak kami dengan Anda. Jika kami ingin menggunakan informasi Anda untuk tujuan baru, kami akan memberi tahu Anda terlebih dahulu.
                </p>
            </section>

            <!-- Cookie dan teknologi serupa -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">4</span>
                    Cookie dan Teknologi Serupa
                </h2>
                <p class="text-sm text-slate-600">
                    Cookie adalah berkas kecil yang disimpan di perangkat Anda. Kami dan penyedia kami menggunakan cookie dan teknologi serupa (seperti penyimpanan lokal, piksel, dan kit pengembangan perangkat lunak) untuk tujuan berikut:
                </p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-slate-600 text-sm">
                    <li><strong>Esensial:</strong> diperlukan agar Layanan berfungsi, misalnya untuk menjaga Anda tetap masuk atau mengingat pilihan yang Anda buat.</li>
                </ul>
                <p class="text-xs sm:text-sm text-slate-500 italic">
                    Anda dapat memblokir atau menghapus cookie di pengaturan browser Anda. Memblokir cookie esensial dapat membuat sebagian Layanan tidak berfungsi.
                </p>
            </section>

            <!-- Kepada siapa kami membagikan informasi -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">5</span>
                    Kepada Siapa Kami Membagikan Informasi
                </h2>
                <p class="text-sm text-slate-600">Kami membagikan data pribadi hanya sebagaimana diuraikan di sini. Penyedia berikut memproses informasi atas nama kami agar kami dapat menjalankan Layanan:</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200">
                        <span class="text-xs font-bold text-emerald-800 uppercase block mb-1">Pemrosesan Pembayaran</span>
                        <p class="font-semibold text-slate-800">Google Play Billing (Google LLC)</p>
                        <a href="https://policies.google.com/privacy" target="_blank" class="text-xs text-blue-600 hover:underline">Kebijakan privasi Google &rarr;</a>
                    </div>

                    <div class="p-3.5 bg-white rounded-xl border border-slate-200">
                        <span class="text-xs font-bold text-emerald-800 uppercase block mb-1">Masuk (Authentication)</span>
                        <p class="font-semibold text-slate-800">Sign in with Google (Google LLC)</p>
                        <a href="https://policies.google.com/privacy" target="_blank" class="text-xs text-blue-600 hover:underline">Kebijakan privasi Google &rarr;</a>
                    </div>

                    <div class="p-3.5 bg-white rounded-xl border border-slate-200">
                        <span class="text-xs font-bold text-emerald-800 uppercase block mb-1">Peta & Fitur Lokasi</span>
                        <p class="font-semibold text-slate-800">Google Maps (Google LLC)</p>
                        <a href="https://policies.google.com/privacy" target="_blank" class="text-xs text-blue-600 hover:underline">Kebijakan privasi Google &rarr;</a>
                    </div>

                    <div class="p-3.5 bg-white rounded-xl border border-slate-200">
                        <span class="text-xs font-bold text-emerald-800 uppercase block mb-1">Keamanan & Anti-Bot</span>
                        <p class="font-semibold text-slate-800">Cloudflare Turnstile (Cloudflare, Inc.)</p>
                        <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" class="text-xs text-blue-600 hover:underline">Kebijakan privasi Cloudflare &rarr;</a>
                    </div>

                    <div class="p-3.5 bg-white rounded-xl border border-slate-200 sm:col-span-2">
                        <span class="text-xs font-bold text-emerald-800 uppercase block mb-1">Fitur Kecerdasan Buatan (AI)</span>
                        <p class="font-semibold text-slate-800">Google Gemini API (Google LLC)</p>
                        <a href="https://ai.google.dev/gemini-api/terms" target="_blank" class="text-xs text-blue-600 hover:underline">Persyaratan & Kebijakan Google Gemini &rarr;</a>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-slate-600">
                    Kami juga dapat membagikan informasi kepada:
                </p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-slate-600 text-xs sm:text-sm">
                    <li>Otoritas, jika hukum mensyaratkannya atau untuk melindungi hak kami dan keselamatan pihak lain.</li>
                    <li>Pembeli atau penerus, jika bisnis kami dijual, digabungkan, atau direorganisasi.</li>
                </ul>
                <p class="font-bold text-emerald-900 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200 text-xs sm:text-sm">
                    Kami tidak menjual data pribadi.
                </p>
            </section>

            <!-- Fitur AI & Transfer Internasional -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">6</span>
                    Fitur AI & Transfer Internasional
                </h2>
                <p class="text-sm text-slate-600">
                    <strong>Fitur AI:</strong> Sebagian fitur menggunakan kecerdasan buatan. Teks atau berkas yang Anda kirimkan ke fitur tersebut dikirim ke penyedia AI kami untuk menghasilkan keluarannya.
                </p>
                <p class="text-sm text-slate-600">
                    <strong>Transfer internasional:</strong> Sebagian penyedia kami berlokasi di negara lain, yang dapat mencakup Amerika Serikat, sehingga informasi Anda dapat ditransfer ke luar negara Anda. Saat kami mentransfer data pribadi ke luar Wilayah Ekonomi Eropa atau Inggris Raya, kami mengandalkan keputusan kecukupan atau klausul kontraktual standar yang disetujui oleh Komisi Eropa atau otoritas Inggris Raya. Anda dapat meminta salinan pengamanan tersebut kepada kami.
                </p>
            </section>

            <!-- Berapa lama kami menyimpan informasi & Perlindungan -->
            <section class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">7</span>
                    Retensi Data & Cara Kami Melindungi Informasi
                </h2>
                <p class="text-sm text-slate-600">
                    Kami menyimpan data pribadi hanya selama diperlukan untuk tujuan dalam kebijakan ini, lalu menghapus atau menganonimkannya. Lamanya bergantung pada jenis informasi, alasan kami menyimpannya, dan apakah hukum mewajibkan kami menyimpannya. Kami dapat menyimpan informasi lebih lama jika hukum mensyaratkannya atau untuk menyelesaikan sengketa.
                </p>
                <p class="text-sm text-slate-600">
                    Kami menerapkan langkah teknis dan organisasi yang wajar untuk melindungi data pribadi. Tidak ada metode transmisi atau penyimpanan yang sepenuhnya aman, sehingga kami tidak dapat menjamin keamanan mutlak.
                </p>
            </section>

            <!-- Hak Anda & Regulasi Internasional -->
            <section class="space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-black">8</span>
                    Hak Anda
                </h2>
                <p class="text-sm text-slate-600">
                    Anda dapat menanyakan kepada kami kapan saja data pribadi apa yang kami simpan tentang Anda, dan meminta kami memperbaiki atau menghapusnya. Untuk itu, tulis ke <a href="mailto:neuracakrawira@yahoo.com" class="text-emerald-800 font-bold hover:underline">neuracakrawira@yahoo.com</a>. Anda dapat meninjau dan mengubah informasi akun Anda di pengaturan akun.
                </p>

                <!-- Accordion / Tabs details for jurisdictions -->
                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="p-4 bg-white rounded-xl border border-slate-200">
                        <h4 class="font-bold text-slate-900 mb-1">Wilayah Ekonomi Eropa dan Inggris Raya (GDPR & UK GDPR)</h4>
                        <p class="text-slate-600 text-xs leading-relaxed mb-2">
                            Anda berhak mengakses data pribadi, meminta perbaikan, penghapusan, pembatasan, menolak penggunaan termasuk untuk pemasaran, menerima data dalam format portabel, serta menarik persetujuan kapan saja. Pengaduan dapat diajukan ke Information Commissioner's Office (ICO) di <a href="https://ico.org.uk/make-a-complaint" target="_blank" class="text-blue-600 underline">ico.org.uk/make-a-complaint</a> atau otoritas pelindungan data di negara Anda.
                        </p>
                        <p class="text-[11px] text-slate-500">Kami tidak membuat keputusan tentang Anda yang semata-mata didasarkan pada pemrosesan otomatis yang menimbulkan akibat hukum signifikan.</p>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200">
                        <h4 class="font-bold text-slate-900 mb-1">Do Not Track</h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Sebagian browser dapat mengirim sinyal “Do Not Track”. Layanan tidak menanggapi sinyal Do Not Track. Kami tidak mengizinkan pihak ketiga mengumpulkan informasi tentang aktivitas online Anda dari waktu ke waktu dan di berbagai situs web saat Anda menggunakan Layanan.
                        </p>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200">
                        <h4 class="font-bold text-slate-900 mb-1">Kanada (PIPEDA) & Brasil (LGPD) & Australia (Privacy Act 1988)</h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Kami menghormati seluruh hak privasi menurut hukum Kanada, LGPD Brasil (kontak encarregado: neuracakrawira@yahoo.com / petisi ANPD gov.br/anpd), dan Australian Privacy Principles (OAIC oaic.gov.au).
                        </p>
                    </div>
                </div>
            </section>

            <!-- Anak-anak, Menghapus Akun, Perubahan & Kontak -->
            <section class="space-y-4 pt-4 border-t border-slate-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-slate-200">
                        <h3 class="font-bold text-slate-900 text-sm mb-1">Perlindungan Anak-anak</h3>
                        <p class="text-xs text-slate-600">
                            Layanan ini tidak ditujukan bagi anak di bawah usia 13 tahun, dan kami tidak dengan sengaja mengumpulkan data pribadi dari mereka. Jika Anda yakin seorang anak telah memberikan data pribadi kepada kami, hubungi kami dan kami akan menghapusnya.
                        </p>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-slate-200">
                        <h3 class="font-bold text-slate-900 text-sm mb-1">Menghapus Akun Anda</h3>
                        <p class="text-xs text-slate-600">
                            Untuk menghapus akun Anda beserta informasi yang terkait, tulis ke <a href="mailto:neuracakrawira@yahoo.com" class="text-emerald-800 font-bold hover:underline">neuracakrawira@yahoo.com</a>.
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-slate-100 rounded-xl text-xs text-slate-600 space-y-1">
                    <p><strong>Perubahan pada kebijakan ini:</strong> Kami dapat memperbarui kebijakan ini. Kami akan memasang versi baru di halaman ini dan mengubah tanggal “Terakhir diperbarui”. Kebijakan Privasi ini dibuat menggunakan Generator Kebijakan Privasi ToolsPivot. Untuk perubahan yang signifikan, kami juga akan memberi tahu Anda melalui email atau pemberitahuan di Layanan.</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-emerald-900 to-slate-900 text-white rounded-2xl">
                    <h3 class="font-bold text-base mb-2">Hubungi Kami</h3>
                    <p class="text-xs text-emerald-100/90 mb-3">Jika Anda memiliki pertanyaan tentang kebijakan ini atau tentang data pribadi Anda, hubungi kami:</p>
                    <div class="text-xs space-y-1 font-mono text-emerald-200">
                        <p><strong class="font-sans text-white">Neura Cakrawira Asia</strong></p>
                        <p>Email: <a href="mailto:neuracakrawira@yahoo.com" class="underline text-emerald-300">neuracakrawira@yahoo.com</a></p>
                        <p>Telepon: +62 895-3831-07479</p>
                        <p>Alamat surat: neuracakrawira@yahoo.com</p>
                    </div>
                </div>
            </section>
        </article>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-5xl mx-auto px-4">
            <p>Aku Sehat &copy; 2026 &bull; Dikelola oleh Neura Cakrawira Asia. Seluruh Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>
