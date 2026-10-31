# 📱 Aku Sehat — Mobile Application (V1)

[![React Native](https://img.shields.io/badge/React_Native-0.81.6-61DAFB?style=for-the-badge&logo=react&logoColor=black)](https://reactnative.dev)
[![React](https://img.shields.io/badge/React-19.1.4-61DAFB?style=for-the-badge&logo=react&logoColor=black)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.8.3-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Android](https://img.shields.io/badge/Platform-Android-3DDC84?style=for-the-badge&logo=android&logoColor=white)](https://developer.android.com)

Aplikasi mobile lintas peran (**Aku Sehat**) berbasis **React Native** dan **TypeScript**. Dikembangkan untuk mempermudah pemantauan kesehatan mandiri (*Member*), konsultasi interaktif (*Health Consultant*), serta tata kelola peminjaman fasilitas medis/pita UKS (*Health Monitor*) secara *real-time*.

---

## 🎨 Dual-Engine UI & Desain Adaptif

Aplikasi dilengkapi mesin tema dinamis (*Theme Engine*) yang dapat diubah langsung dari aplikasi melalui **Theme Hub Modal**:

### 1. Emerald Classic (Tema Medis Klinis)
- Palet warna hijau emerald bersih bernuansa medis tradisional.
- Bilah navigasi bawah (*bottom bar*) *edge-to-edge* standar dengan aksen titik indikator aktif.
- Didesain untuk keterbacaan tinggi dan kenyamanan mata di lingkungan pencahayaan terang.

### 2. Atlas Neo Modern (Tema Cyber Telemetry & Eksklusif Dark Mode)
- **Expanding Capsule Pill Navigation**: Bilah navigasi bawah mengadopsi kapsul dinamis yang melebar horizontal pada tab aktif dengan transisi pegas (*spring animation*), sedangkan tab tidak aktif tampil minimalis.
- **Continuous Light Sweep Shimmer**: Header kartu informasi dilengkapi animasi kilau cahaya berjalan (*light sweep sheen*) dengan kalkulasi kliping presisi (*overflow hidden* & *border radius*).
- **Dark Mode Eksklusif**: Mode gelap penuh dengan palet slate/neon cyber (`#0F172A`, `#22D3EE`, `#6366F1`).
- **On-Demand Remote Theme Engine**: Definisi tema dapat diunduh secara *on-demand* dari server/GitHub CDN dan disimpan dalam penyimpanan lokal perangkat (`AsyncStorage` / FileSystem).

### 3. Sistem Skala Responsif
Menggunakan utility `utils/responsive.ts` (`horizontalScale`, `verticalScale`, `moderateScale`) sehingga tata letak, ukuran font, dan elemen interaktif otomatis proporsional di seluruh ukuran layar smartphone maupun tablet.

---

## 🚀 Fitur Utama Berdasarkan Peran

### 1. Panel Member
- **Ringkasan Kesehatan & BMI**: Kalkulasi Indeks Massa Tubuh instan beserta status klasifikasi (Kurang, Normal, Kelebihan, Obesitas).
- **Skrining Klinis**: Riwayat pemeriksaan fisik berkala (Tinggi/Berat Badan, Mata, Gigi, Telinga, Reproduksi).
- **Hemoglobin & Haid**: Pemantauan kadar Hb dan pencatatan siklus menstruasi bagi siswi/anggota putri.
- **Aktivitas & Vitalitas**: Pelacakan olahraga/lari berbasis sensor dan rekap data vital tubuh.
- **Peminjaman Pita UKS**: Notifikasi otomatis terkait batas waktu pengembalian pita kesehatan dan status persetujuan.

### 2. Panel Health Consultant (Konselor)
- **Monitoring Divisi**: Pemantauan rekapitulasi kesehatan seluruh anggota dalam divisi binaan.
- **Sesi Konseling**: Penerimaan pengajuan konseling, penjadwalan tatap muka, dan pencatatan hasil evaluasi.
- **Pusat Notifikasi**: Notifikasi otomatis saat ada anggota yang membutuhkan perhatian khusus atau kondisi anomali.

### 3. Panel Health Monitor (Petugas UKS)
- **Validasi Peminjaman Pita**: Verifikasi permohonan pinjam pita/perlengkapan UKS.
- **Peringatan & Keterlambatan Otomatis**: Deteksi anggota yang melewati batas waktu pengembalian pita dengan sistem peringatan terintegrasi.

---

## 🛠️ Persyaratan Lingkungan Pengembangan

- **Node.js**: `>= 20.x`
- **NPM**: `>= 10.x` atau **Yarn**
- **JDK (Java Development Kit)**: OpenJDK 17
- **Android Studio**: Android SDK Platform 34 / 35, Android Build Tools 34+, Android SDK Command-line Tools
- **Perangkat / Emulator**: Android 8.0 (Oreo) ke atas

---

## ⚙️ Panduan Instalasi & Menjalankan Aplikasi

### 1. Kloning Repository & Checkout Branch
```bash
git clone https://github.com/dapzz-id/AkuSehat.git
cd AkuSehat/AkuSehat
git checkout Mobile_V1
```

### 2. Pasang Dependensi
```bash
npm install
```

### 3. Konfigurasi Endpoint API
Buka file `src/settings/Config.tsx` dan sesuaikan alamat API backend:
```typescript
export const Config = {
    APP_NAME: "Aku Sehat",
    APP_VERSION: "1.0.0",
    APP_BUILD: "Build Stabil",
    PACKAGE_NAME: "neuracakrawira.asia.akusehat",
    // Gunakan 10.0.2.2 untuk Android Emulator atau IP LAN untuk HP fisik
    API_URL: Platform.OS === "android" ? "http://10.0.2.2:8000/api" : "http://localhost:8000/api",
    AXIOS_TIMEOUT: 15000,
    AXIOS_CREDENTIALS: true,
}
```

### 4. Menjalankan Metro Bundler
```bash
npm start
```

### 5. Menjalankan di Emulator / Perangkat Android
Di terminal terpisah, jalankan:
```bash
npm run android
```

---

## 📦 Panduan Rilis Android (APK & AAB)

Proyek ini telah dikonfigurasi untuk pembuatan bundel rilis dengan Proguard dan penandatanganan keystore terisolasi via `android/gradle.properties`:

### 1. Konfigurasi Keystore
Pastikan file `gradle.properties` (atau `~/.gradle/gradle.properties`) memuat variabel berikut:
```properties
AKUSEHAT_UPLOAD_STORE_FILE=D:/Keystore/akusehat-upload-key.keystore
AKUSEHAT_UPLOAD_STORE_PASSWORD=your_keystore_password
AKUSEHAT_UPLOAD_KEY_ALIAS=your_key_alias
AKUSEHAT_UPLOAD_KEY_PASSWORD=your_key_password
```

### 2. Kompilasi APK Rilis
```bash
cd android
./gradlew assembleRelease
```
*Hasil APK terletak di:* `android/app/build/outputs/apk/release/app-release.apk`

### 3. Kompilasi Android App Bundle (AAB - Google Play Store)
```bash
cd android
./gradlew bundleRelease
```
*Hasil AAB terletak di:* `android/app/build/outputs/bundle/release/app-release.aab`

---

## 📁 Struktur Direktori Utama

```text
AkuSehat/
├── android/                    # Konfigurasi native Android (Gradle, Proguard, Manifest)
├── src/
│   ├── components/             # Komponen UI (AnimatedBottomTabs, ThemeHubModal, Modals)
│   ├── navigation/             # Setup navigasi aplikasi
│   ├── screens/
│   │   ├── auth/               # Layar Login, Registrasi & Verifikasi
│   │   ├── health-consultant/  # Dashboard Konselor UKS
│   │   ├── health-monitor/     # Dashboard Petugas Pita UKS
│   │   └── member/             # Dashboard Anggota (Ringkasan, Klinis, Hb, Profil)
│   ├── settings/               # Warna (Colors.ts) dan Konfigurasi API (Config.tsx)
│   ├── theme/                  # ThemeContext, themeStorage, Palet Atlas & Emerald
│   └── utils/                  # responsive.ts, date helpers, storage wrapper
├── themes/                     # Template file JSON tema modern (Atlas Neo)
├── App.tsx                     # Root component aplikasi
└── index.js                    # Entry point React Native
```

---

## 🔒 Lisensi & Hak Cipta

Hak Cipta © 2026 **Aku Sehat**. Seluruh hak cipta dilindungi undang-undang.
