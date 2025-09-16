@extends('layouts.app')

@section('title', 'Warning Peminjaman Terlambat')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Warning Peminjaman Terlambat</h2>
        <p class="text-gray-600">Daftar siswi yang terlambat mengembalikan pita</p>
    </div>

    @if($terlambat->count() > 0)
        <div class="bg-red-50 border border-red-200 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Ada {{ $terlambat->count() }} peminjaman yang terlambat!
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Segera hubungi siswi berikut untuk mengembalikan pita atau konfirmasi status mereka.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Siswi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Pinjam
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estimasi Selesai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Terlambat
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah Pita
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($terlambat as $item)
                            <tr class="hover:bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->user->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->user->nis }} - {{ $item->user->kelas->kelas ?? 'No Class' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->estimasi_selesai_haid->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $item->hariTerlambat() }} hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->jumlah_pita }} buah
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('guru-bk.peminjaman.kembali', $item->id) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="tanggal_kembali" value="{{ date('Y-m-d') }}">
                                        <button type="submit" class="text-green-600 hover:text-green-900"
                                                onclick="return confirm('Konfirmasi pengembalian pita?')">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Kembalikan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">
                        Tidak ada peminjaman yang terlambat
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Semua peminjaman pita dalam status normal atau sudah dikembalikan.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection