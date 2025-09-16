@extends('layouts.app')

@section('title', 'Peminjaman Pita')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Peminjaman Pita</h2>
        <a href="{{ route('guru-bk.peminjaman.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Pinjam Baru
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Pinjam
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah Pita
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estimasi Selesai
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($peminjaman as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->user->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $item->user->nis }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->tanggal_pinjam->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->jumlah_pita }} buah
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->estimasi_selesai_haid->format('d/m/Y') }}
                                @if($item->isTerlambat())
                                    <span class="text-red-500 text-xs block">
                                        ({{ $item->hariTerlambat() }} hari terlambat)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($item->status) {
                                        'dipinjam' => 'bg-blue-100 text-blue-800',
                                        'dikembalikan' => 'bg-green-100 text-green-800',
                                        'terlambat' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($item->status !== 'dikembalikan')
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
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $peminjaman->links() }}
        </div>
    </div>
</div>
@endsection