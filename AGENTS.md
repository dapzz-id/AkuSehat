# Aturan Keamanan & Privasi Workspace (Strict Sensitive File Protection)

1. **Larangan Mutlak Membaca File Sensitif Tanpa Izin Eksplisit Pemilik**:
   - AI **DILARANG KERAS** membaca, menampilkan, atau mengakses `.env`, `.env.*` (kecuali `.env.example`), `*.pem`, `*.key`, `auth.json`, atau file kredensial/database produksi baik melalui `read_file` maupun perintah terminal (`Get-Content`, `cat`, `type`, `Select-String`, `grep`, dll.) tanpa meminta izin dan mendapat persetujuan eksplisit dari pemilik (User).
2. **Prosedur Penambahan Variabel Environment**:
   - Untuk menambahkan variabel konfigurasi baru, AI hanya boleh mengubah `.env.example` dan`config/*.php`, kemudian memberi tahu User variabel apa yang perlu ditambahkan sendiri ke `.env`.
3. **Larangan Hardcode Secret**:
   - Jangan pernah menaruh kunci rahasia (*secret key*, *API key*, *password*) secara *hardcoded* di dalam file yang di-*commit* ke GitHub.
