<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function indexSiswa(Request $request)
    {
        $query = User::with(['kelas', 'kesehatan'])
            ->where('level', 'Siswa')
            ->orderBy('nama');

        if ($request->has('kelas') && $request->kelas != '') {
            $query->where(function($q) use ($request) {
                $q->where('id_kelas', $request->kelas);
            });
        }

        $dataCount = (clone $query)->get();

        $siswa = $query->paginate(5)->withQueryString();

        return view('admin.siswa.index', compact('siswa', 'dataCount'));
    }
}