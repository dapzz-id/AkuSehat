<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kesehatan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KesehatanApiController extends Controller
{
    // public function getKesehatanMember(Request $request, $id)
    // {
    //     $user = $request->user();
        
    //     if ($user->isMember() && $user->id != $id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Akses tidak diperbolehkan.'
    //         ], 403);
    //     }

    //     $kesehatan = Kesehatan::where('id_user', $id)
    //         ->orderBy('tgl', 'desc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $kesehatan
    //     ]);
    // }

    public function getKesehatanMember(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->isMember() && $user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        // 🩺 Ambil semua data kesehatan user
        $kesehatan = Kesehatan::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->get();

        // 🗓️ Kelompokkan berdasarkan tahun dan bulan
        $grouped = $kesehatan->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tgl)->format('m');
            })->map(function ($monthGroup, $month) {
                // Format nama bulan biar enak dibaca
                $monthName = \Carbon\Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('F');
                return [
                    'month' => strtolower($monthName), // jadi huruf kecil semua
                    'data' => $monthGroup->values(),   // isi data kesehatan per bulan
                ];
            })->values();
        });

        // 🔁 Ubah struktur supaya rapi
        $formatted = $grouped->map(function ($months, $year) {
            return [
                'year' => (int) $year,
                'months' => $months,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    public function getKesehatanDivisi(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isHealthConsultant()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $memberIds = User::where('level', 'Member')
            ->where('id_divisi', $id)
            ->pluck('id');

        $kesehatan = Kesehatan::with('user')
            ->whereIn('id_user', $memberIds)
            ->orderBy('tgl', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $kesehatan
        ]);
    }

    public function getStatistikDivisi(Request $request, $divisi_id)
    {
        $user = $request->user();
        
        if (!$user->isHealthConsultant()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $memberIds = User::where('level', 'Member')
            ->where('id_divisi', $divisi_id)
            ->pluck('id');

        $statistik = Kesehatan::whereIn('id_user', $memberIds)
            ->whereIn('id_kesehatan', function($query) use ($memberIds) {
                $query->select(DB::raw('MAX(id_kesehatan)'))
                    ->from('kesehatan')
                    ->whereIn('id_user', $memberIds)
                    ->groupBy('id_user');
            })
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $statistik
        ]);
    }

    public function getAIRecommendation(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'jk' => 'required|in:L,P',
            'umur' => 'required|integer',
            'bb' => 'required|numeric',
            'tb' => 'required|numeric',
            'imt' => 'required|numeric',
            'status_imt' => 'required|string',
        ]);

        try {
            $apiKey = env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key Gemini tidak tersedia.'
                ], 500);
            }

            $prompt = "Berikan saran kesehatan untuk member dengan data berikut:\n" .
                     "Nama: {$request->nama}\n" .
                     "Jenis Kelamin: " . ($request->jk === 'L' ? 'Laki-laki' : 'Perempuan') . "\n" .
                     "Umur: {$request->umur} tahun\n" .
                     "Berat Badan: {$request->bb} kg\n" .
                     "Tinggi Badan: {$request->tb} cm\n" .
                     "IMT: {$request->imt}\n" .
                     "Status IMT: {$request->status_imt}\n\n" .
                     "Berikan saran praktis tentang:\n" .
                     "1. Pola makan yang sehat\n" .
                     "2. Olahraga yang sesuai\n" .
                     "3. Tips menjaga kesehatan\n" .
                     "4. Hal-hal yang perlu dihindari\n\n" .
                     "Jawab dalam bahasa Indonesia dengan maksimal 300 kata.";

            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $recommendation = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak dapat menghasilkan rekomendasi.';
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'recommendation' => $recommendation
                    ]
                ]);
            } else {
                throw new \Exception('API request failed');
            }
            
        } catch (\Exception $e) {
            // Fallback jika API gagal
            $fallbackRecommendation = $this->getFallbackRecommendation($request->status_imt, $request->jk);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'recommendation' => $fallbackRecommendation
                ]
            ]);
        }
    }

    private function getFallbackRecommendation($status_imt, $jk)
    {
        $gender = $jk === 'L' ? 'laki-laki' : 'perempuan';
        
        switch ($status_imt) {
            case 'Kurus':
                return "Untuk menaikkan berat badan yang sehat:\n\n" .
                       "1. **Pola Makan**: Makan 5-6 kali sehari dengan porsi kecil. Pilih makanan bergizi tinggi seperti kacang-kacangan, alpukat, dan protein.\n" .
                       "2. **Olahraga**: Latihan kekuatan ringan untuk membangun massa otot.\n" .
                       "3. **Tips**: Minum susu atau smoothie tinggi protein. Istirahat cukup 7-8 jam.\n" .
                       "4. **Hindari**: Makanan junk food dan minuman bersoda berlebihan.";
                       
            case 'Overweight':
                return "Untuk menurunkan berat badan yang sehat:\n\n" .
                       "1. **Pola Makan**: Kurangi porsi makan, perbanyak sayuran dan buah. Hindari makanan berlemak tinggi.\n" .
                       "2. **Olahraga**: Kombinasi kardio (jogging, bersepeda) dan latihan kekuatan 3-4x seminggu.\n" .
                       "3. **Tips**: Minum air putih 8 gelas sehari. Catat asupan makanan harian.\n" .
                       "4. **Hindari**: Makanan cepat saji, minuman manis, dan cemilan berkalori tinggi.";
                       
            case 'Obesitas':
                return "Program penurunan berat badan intensif:\n\n" .
                       "1. **Pola Makan**: Diet rendah kalori dengan bimbingan ahli gizi. Makan dalam porsi kecil tapi sering.\n" .
                       "2. **Olahraga**: Mulai dengan olahraga ringan seperti jalan kaki, kemudian tingkatkan intensitas bertahap.\n" .
                       "3. **Tips**: Konsultasi dengan dokter. Pantau berat badan mingguan. Dukungan keluarga sangat penting.\n" .
                       "4. **Hindari**: Diet ekstrem, makanan olahan, dan gaya hidup sedentari.";
                       
            default:
                return "Untuk mempertahankan berat badan ideal:\n\n" .
                       "1. **Pola Makan**: Makan seimbang dengan 4 sehat 5 sempurna. Porsi sayuran lebih banyak.\n" .
                       "2. **Olahraga**: Olahraga rutin 30 menit setiap hari, variasikan jenis olahraga.\n" .
                       "3. **Tips**: Jaga pola tidur teratur. Kelola stress dengan baik. Check-up kesehatan berkala.\n" .
                       "4. **Hindari**: Pola makan tidak teratur dan kurang aktivitas fisik.";
        }
    }
}