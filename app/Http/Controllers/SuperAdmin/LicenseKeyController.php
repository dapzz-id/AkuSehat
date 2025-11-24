<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LicenseKey;

class LicenseKeyController extends Controller
{
    public function index()
    {
        $licenses = LicenseKey::with('sekolah')->paginate(10);
        // \dd($licenses->toArray());
        return view('superadmin.license.index', compact('licenses'));
    }

    public function create()
    {
        return view('superadmin.license.create');
    }
}
