@extends('layouts.app')

@php
    $user = auth()->user();
    if (!isset($roleView)) {
        if ($user && $user->isSuperAdmin()) {
            $roleView = 'superadmin';
        } elseif ($user && ($user->isAdmin() || $user->isAdminInstansi() || $user->isHealthMonitor() || $user->isHealthConsultant())) {
            $roleView = 'admin';
        } else {
            $roleView = 'member';
        }
    }

    $roleTitles = [
        'superadmin' => 'Dashboard SuperAdmin',
        'admin' => 'Dashboard Admin',
        'member' => 'Dashboard Member',
    ];
    $pageTitle = $roleTitles[$roleView] ?? 'Dashboard';
@endphp

@section('title', $pageTitle)

@section('content')
{{-- Atlas Theme --}}
<div id="theme-atlas-container" class="space-y-6 sm:space-y-8">
    @include("dashboard.theme.atlas.{$roleView}")
</div>

{{-- Emerald Theme --}}
<div id="theme-default-container" class="space-y-6 sm:space-y-8 hidden">
    @include("dashboard.theme.emerald.{$roleView}")
</div>
@endsection
