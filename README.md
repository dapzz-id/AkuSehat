# Sistem Manajemen Kesehatan Siswa/i - Healthy+

Sistem manajemen kesehatan sekolah dengan fitur lengkap untuk Admin PMR dan Guru BK, serta API untuk aplikasi mobile Guru Olahraga dan Siswa.

## Fitur Utama

### Web Application
- **Admin PMR**: Manajemen data kesehatan siswa (TB, BB, HB, Telinga, Gigi, Perilaku beresiko, Gangguan reproduksi)
- **Guru BK**: Manajemen peminjaman pita dengan sistem warning otomatis

### Mobile API
- **Guru Olahraga**: Monitoring kesehatan semua siswa dan statistik kelas
- **Siswa**: Melihat rekap data pribadi dan mendapat rekomendasi AI

## Teknologi

- Laravel 12
- MySQL Database
- Tailwind CSS (Responsive Design)
- Laravel Sanctum (API Authentication)
- Google Gemini AI Integration
- Alpine.js untuk interaktivitas

## Default Credentials

- **Admin PMR**: username: `adminpmr`, password: `password123`
- **Guru BK**: username: `gurubk`, password: `password123`
- **Guru Olahraga**: username: `guriolahraga`, password: `password123`
- **Siswa**: username: `siswa1-siswa50`, password: `password123`

## Fitur Keamanan

- Role-based access control
- CSRF protection
- Input validation
- Password hashing
- API rate limiting

## AI Integration

Sistem terintegrasi dengan Google Gemini AI untuk memberikan rekomendasi kesehatan personal berdasarkan data BMI, jenis kelamin, dan kondisi kesehatan siswa.

## Responsive Design

Desain responsive yang optimal untuk desktop, tablet, dan mobile menggunakan Tailwind CSS dengan pendekatan mobile-first.

## Support

Untuk pertanyaan atau bantuan, silakan hubungi developer atau buat issue di repository ini.

## License

Berbayar untuk backend - silakan hubungi administrator untuk kerjasama