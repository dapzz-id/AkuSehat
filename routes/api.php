<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// MODULAR API ROUTES (Domain/Feature-Driven)
// ==========================================
require base_path('app/Modules/User/api.php');
require base_path('app/Modules/Mens/api.php');
require base_path('app/Modules/Kesehatan/api.php');
require base_path('app/Modules/Hb/api.php');
require base_path('app/Modules/Divisi/api.php');
require base_path('app/Modules/Billing/api.php');
require base_path('app/Modules/SuperAdmin/api.php');