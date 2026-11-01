# Aturan Keamanan & Privasi Workspace (Strict Sensitive File Protection)

1. **Larangan Mutlak Membaca File Sensitif Tanpa Izin Eksplisit Pemilik**:
   - AI **DILARANG KERAS** membaca, menampilkan, atau mengakses `.env`, `.env.*` (kecuali `.env.example`), `src/config/secrets.local.ts`, `*.keystore`, `*.jks`, `local.properties`, atau `google-services.json` baik melalui `read_file` maupun perintah terminal (`Get-Content`, `cat`, `type`, `Select-String`, `grep`, dll.) tanpa meminta izin dan mendapat persetujuan eksplisit dari pemilik (User).
2. **Prosedur Penambahan Secret / Konfigurasi Baru**:
   - Untuk menambahkan variabel rahasia baru, AI hanya boleh memperbarui `src/config/secrets.example.ts` (atau `.env.example`) dan memberi tahu User variabel apa yang perlu diisi sendiri di `src/config/secrets.local.ts`.
3. **Larangan Hardcode Secret ke GitHub**:
   - Jangan pernah menaruh kunci rahasia (*S2S shared secret*, *API key*, *keystore password*) di dalam file kode yang ter-publish ke GitHub.
