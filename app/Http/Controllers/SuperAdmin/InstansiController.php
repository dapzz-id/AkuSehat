<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instansi;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class InstansiController extends Controller
{
    public function index()
    {
        $instansi = Instansi::with(['licenseKey'])->latest()->paginate(10);
        return view('superadmin.instansi.index', compact('instansi'));
    }

    public function create()
    {
        $province = $this->getProvinsi();
        return view('superadmin.instansi.create', compact('province'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:instansi,npsn',
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
            'npsn.unique' => 'NPSN sudah terdaftar pada instansi lain.',
            'nama_instansi.required' => 'Nama instansi harus diisi.',
            'nama_instansi.max' => 'Nama instansi maksimal 255 karakter.',
            'alamat.required' => 'Alamat instansi harus diisi.',
            'alamat.string' => 'Alamat instansi harus berupa teks.',
            'kota.string' => 'Kota/Kabupaten harus berupa teks.',
            'kota.max' => 'Kota/Kabupaten maksimal 100 karakter.',
            'provinsi.string' => 'Provinsi harus berupa teks.',
            'provinsi.max' => 'Provinsi maksimal 100 karakter.',
            'npsn.required' => 'NPSN harus diisi.',
            'npsn.string' => 'NPSN harus berupa teks.',
            'npsn.max' => 'NPSN maksimal 20 karakter.',
        ]);

        $instansi = Instansi::create($request->only([
            'nama_instansi', 'npsn', 'alamat', 'kota', 'provinsi',
            'kode_pos', 'telepon', 'website', 'email', 'jenjang'
        ]));

        User::create([
            'nama' => $instansi->nama_instansi,
            'username' => $instansi->npsn,
            'password' => bcrypt('password123'),
            'level' => 'Admin Instansi',
            'instansi_id' => $instansi->id,
            'jk' => 'L',
            'tgl' => now(),
            'nis' => null,
            'id_divisi' => null,
            'license_key_id' => null
        ]);

        return redirect()->route('superadmin.instansi.index')
            ->with('success', "Data instansi '{$instansi->nama_instansi}' berhasil ditambahkan.");
    }

    public function edit(Instansi $instansi)
    {
        $province = $this->getProvinsi();
        return view('superadmin.instansi.edit', compact('instansi', 'province'));
    }

    public function update(Request $request, Instansi $instansi)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:instansi,npsn,' . $instansi->id,
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
            'npsn.unique' => 'NPSN sudah terdaftar pada instansi lain.',
            'nama_instansi.required' => 'Nama instansi harus diisi.',
            'nama_instansi.max' => 'Nama instansi maksimal 255 karakter.',
            'alamat.required' => 'Alamat instansi harus diisi.',
            'alamat.string' => 'Alamat instansi harus berupa teks.',
            'kota.string' => 'Kota/Kabupaten harus berupa teks.',
            'kota.max' => 'Kota/Kabupaten maksimal 100 karakter.',
            'provinsi.string' => 'Provinsi harus berupa teks.',
            'provinsi.max' => 'Provinsi maksimal 100 karakter.',
            'npsn.required' => 'NPSN harus diisi.',
            'npsn.string' => 'NPSN harus berupa teks.',
            'npsn.max' => 'NPSN maksimal 20 karakter.',
        ]);

        $instansi->update($request->only([
            'nama_instansi', 'npsn', 'alamat', 'kota', 'provinsi',
            'kode_pos', 'telepon', 'website', 'email', 'jenjang'
        ]));

        return redirect()->route('superadmin.instansi.index')
            ->with('success', "Data instansi '{$instansi->nama_instansi}' berhasil diupdate.");
    }

    public function destroy(Instansi $instansi)
    {
        if ($instansi->users()->exists()) {
            $instansi->users()->delete();
        }

        $nama = $instansi->nama_instansi;
        $instansi->delete();

        return redirect()->route('superadmin.instansi.index')
            ->with('success', "Data instansi '{$nama}' berhasil dihapus.");
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
