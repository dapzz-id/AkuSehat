<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SoftwareApp;

class SoftwareAppController extends Controller
{
    public function index()
    {
        $updates = SoftwareApp::paginate(10);
        return view('superadmin.software.index', compact('updates'));
    }

    public function downloadApp($id)
    {
        $softwareApp = SoftwareApp::findOrFail($id);
        $link = $softwareApp->link;
        
        return redirect($link);
    }
}
