<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::with(['licenseKey'])->latest()->paginate(10);
        return view('superadmin.sekolah.index', compact('sekolah'));
    }

    public function create()
    {
        $province = $this->getProvinsi();
        return view('superadmin.sekolah.create', compact('province'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:sekolah,npsn',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'website' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'jenjang' => 'required|in:SMP,SMA,SMK,MA,SLB,MTS',
        ],[
            'provinsi.required' => 'Provinsi harus diisi.',
            'kota.required' => 'Kota/Kabupaten harus diisi.',
            'jenjang.required' => 'Jenjang harus diisi.',
            'jenjang.in' => 'Jenjang tidak valid. Pilih dari SMP, SMA, SMK, MA, SLB, MTS.',
            'kode_pos.max' => 'Kode Pos maksimal 10 karakter.',
            'telepon.max' => 'No. Telepon maksimal 20 karakter.',
            'website.max' => 'Website maksimal 100 karakter.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'npsn.unique' => 'NPSN sudah terdaftar pada sekolah lain.',
            'nama_sekolah.required' => 'Nama sekolah harus diisi.',
            'nama_sekolah.max' => 'Nama sekolah maksimal 255 karakter.',
            'alamat.required' => 'Alamat sekolah harus diisi.',
            'alamat.string' => 'Alamat sekolah harus berupa teks.',
            'kota.string' => 'Kota/Kabupaten harus berupa teks.',
            'kota.max' => 'Kota/Kabupaten maksimal 100 karakter.',
            'provinsi.string' => 'Provinsi harus berupa teks.',
            'provinsi.max' => 'Provinsi maksimal 100 karakter.',
            'npsn.required' => 'NPSN harus diisi.',
            'npsn.string' => 'NPSN harus berupa teks.',
            'npsn.max' => 'NPSN maksimal 20 karakter.',
        ]);

        $sekolah = Sekolah::create($request->only([
            'nama_sekolah', 'npsn', 'alamat', 'kota', 'provinsi',
            'kode_pos', 'telepon', 'website', 'email', 'jenjang'
        ]));

        User::create([
            'nama' => $sekolah->nama_sekolah,
            'username' => $sekolah->npsn,
            'password' => bcrypt('password123'),
            'level' => 'Admin Sekolah',
            'sekolah_id' => $sekolah->id,
            'jk' => 'L',
            'tgl' => now(),
            'nis' => null,
            'id_kelas' => null,
            'license_key_id' => null
        ]);

        return redirect()->route('superadmin.sekolah.index')
            ->with('success', "Data sekolah '{$sekolah->nama_sekolah}' berhasil ditambahkan.");
    }

    public function edit(Sekolah $sekolah)
    {
        $province = $this->getProvinsi();
        return view('superadmin.sekolah.edit', compact('sekolah', 'province'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:sekolah,npsn,' . $sekolah->id,
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'jenjang' => 'required|in:SMP,SMA,SMK,MA,SLB,MTS',
        ],[
            'provinsi.required' => 'Provinsi harus diisi.',
            'kota.required' => 'Kota/Kabupaten harus diisi.',
            'jenjang.required' => 'Jenjang harus diisi.',
            'jenjang.in' => 'Jenjang tidak valid. Pilih dari SMP, SMA, SMK, MA, SLB, MTS.',
            'kode_pos.max' => 'Kode Pos maksimal 10 karakter.',
            'telepon.max' => 'No. Telepon maksimal 20 karakter.',
            'website.max' => 'Website maksimal 100 karakter.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'npsn.unique' => 'NPSN sudah terdaftar pada sekolah lain.',
            'nama_sekolah.required' => 'Nama sekolah harus diisi.',
            'nama_sekolah.max' => 'Nama sekolah maksimal 255 karakter.',
            'alamat.required' => 'Alamat sekolah harus diisi.',
            'alamat.string' => 'Alamat sekolah harus berupa teks.',
            'kota.string' => 'Kota/Kabupaten harus berupa teks.',
            'kota.max' => 'Kota/Kabupaten maksimal 100 karakter.',
            'provinsi.string' => 'Provinsi harus berupa teks.',
            'provinsi.max' => 'Provinsi maksimal 100 karakter.',
            'npsn.required' => 'NPSN harus diisi.',
            'npsn.string' => 'NPSN harus berupa teks.',
            'npsn.max' => 'NPSN maksimal 20 karakter.',
        ]);

        $sekolah->update($request->only([
            'nama_sekolah', 'npsn', 'alamat', 'kota', 'provinsi',
            'kode_pos', 'telepon', 'website', 'email', 'jenjang'
        ]));

        return redirect()->route('superadmin.sekolah.index')
            ->with('success', "Data sekolah '{$sekolah->nama_sekolah}' berhasil diupdate.");
    }

    public function destroy(Sekolah $sekolah)
    {
        if ($sekolah->users()->exists()) {
            $sekolah->users()->delete();
        }

        $nama = $sekolah->nama_sekolah;
        $sekolah->delete();

        return redirect()->route('superadmin.sekolah.index')
            ->with('success', "Data sekolah '{$nama}' berhasil dihapus.");
    }

    // ✅ Ambil daftar provinsi
    public function getProvinsi()
    {
        $response = Http::get('https://api.datawilayah.com/api/provinsi.json');

        if ($response->failed()) {
            return [];
        }

        $json = $response->json();
        return $json['data'] ?? []; // hanya ambil bagian "data"
    }

    // ✅ Ambil kabupaten/kota berdasarkan kode provinsi
    public function getKabupaten(Request $request)
    {
        $provinceId = $request->query('province_id');

        if (!$provinceId) {
            return response()->json(['error' => 'Parameter province_id diperlukan'], 400);
        }

        $response = Http::get("https://api.datawilayah.com/api/kabupaten_kota/{$provinceId}.json");

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal mengambil data'], 500);
        }

        $json = $response->json();
        return response()->json($json['data'] ?? []);
    }
}
