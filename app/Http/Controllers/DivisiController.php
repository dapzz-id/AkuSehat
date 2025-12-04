<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Divisi;
use Illuminate\Support\Facades\DB;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Divisi::withCount(['users as jumlah_member' => function($query) {
            $query->whereNotNull('instansi_id')->where('instansi_id', Auth::user()->instansi_id);
        }])->with(['kesehatan'])->where('instansi_id', Auth::user()->instansi_id);

        if ($request->has('search') && $request->search != '') {
            $query->where('divisi_name', 'like', '%' . $request->search . '%')
            ->orWhere('color_cover', 'like', '%' . $request->search . '%');
        }

        $divisi = $query->orderBy('divisi_name', 'asc')
                ->paginate(10)
                ->withQueryString();

        return view('admin.divisi.index', compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:35',
            'cover_color' => 'required|string|max:20',
        ],[
            'divisi.required' => 'Divisi harus diisi.',
            'divisi.max' => 'Divisi maksimal 35 karakter.',
            'cover_color.required' => 'Warna cover harus diisi.',
            'cover_color.max' => 'Warna cover maksimal 20 karakter.',
        ]);

        if (!Auth::user()->instansi_id) {
            return redirect()->route('admin.divisi.index')
                ->with('error', 'Anda tidak memiliki instansi terkait. Silakan hubungi administrator.');
        }

        Divisi::create([
            'instansi_id' => Auth::user()->instansi_id,
            'tgl' => now()->format('Y-m-d'),
            'divisi_name' => $request->divisi,
            'color_cover' => $request->cover_color,
        ]);

        return redirect()->route('admin.divisi.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Divisi $divisi)
    {
        try {
            $validated = $request->validate([
                'divisi' => 'required|string|max:35|unique:divisi,divisi_name,' . $divisi->id,
                'cover_color' => 'required|string|max:20',
            ], [
                'divisi.unique' => 'Nama divisi sudah ada.',
                'cover_color.required' => 'Warna cover harus diisi.',
                'cover_color.max' => 'Warna cover maksimal 20 karakter.',
                'divisi.required' => 'Divisi harus diisi.',
                'divisi.max' => 'Divisi maksimal 35 karakter.'
            ]);

            // Cek apakah user sudah memiliki instansi terkait
            if (!Auth::user()->instansi_id) {
                return redirect()->route('admin.divisi.index')
                    ->with('error', 'Anda tidak memiliki instansi terkait. Silakan hubungi administrator.');
            }

            Divisi::updateOrCreate([
                'id' => $divisi->id,
                'instansi_id' => Auth::user()->instansi_id,
            ], [
                'divisi_name' => $request->divisi,
                'color_cover' => $request->cover_color,
            ]);

            return redirect()->route('admin.divisi.index')
                ->with('success', 'Divisi berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.divisi.index')
                ->with('error', implode(', ', collect($e->errors())->flatten()->toArray()));
        } catch (\Exception $e) {
            return redirect()->route('admin.divisi.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui divisi: ' . $e->getMessage());
        }
    }

    public function destroy(Divisi $divisi)
    {
        try {
            // Cek apakah user sudah memiliki instansi terkait
            if (!Auth::user()->instansi_id) {
                return redirect()->route('admin.divisi.index')
                    ->with('error', 'Anda tidak memiliki instansi terkait. Silakan hubungi administrator.');
            }

            // Pastikan divisi milik instansi user yang sedang login
            if ($divisi->instansi_id !== Auth::user()->instansi_id) {
                return redirect()->route('admin.divisi.index')
                    ->with('error', 'Anda tidak memiliki izin untuk menghapus divisi ini.');
            }

            $divisi->delete();

            return redirect()->route('admin.divisi.index')
                ->with('success', 'Divisi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.divisi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus divisi: ' . $e->getMessage());
        }
    }

    public function mass_destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:divisi,id',
        ]);

        $dataAdmin = Auth::user();
        if (!$dataAdmin->instansi_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data divisi. Silakan hubungi operator instansi.');
        }

        $deletedCount = 0;

        try {
            DB::transaction(function () use ($request, $dataAdmin, &$deletedCount) {
                foreach ($request->ids as $id) {
                    $divisi = Divisi::find($id);
                    if ($divisi && $divisi->instansi_id === $dataAdmin->instansi_id) {
                        $divisi->delete();
                        $deletedCount++;
                    }
                }
            });

            return redirect()->route('admin.divisi.index')
                ->with('success', "$deletedCount data divisi berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat menghapus: ' . $e->getMessage());
        }
    }
}