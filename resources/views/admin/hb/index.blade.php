@extends('layouts.app')

@section('title', 'Data HB')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-tint text-red-500"></i>
            Data Hemoglobin (HB)
        </h2>
        <a href="{{ route('admin.hb.create') }}" 
           class="inline-flex items-center bg-[#308a34] hover:bg-[#1B5E20] text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Data HB
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">HB (g/dL)</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Pesan</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hb as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Siswa -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $item->user->nama }}</div>
                                <div class="text-gray-500 text-xs">{{ $item->user->nis }} ({{ $item->user->jk === 'L' ? 'L' : 'P' }})</div>
                            </td>
                            <!-- Kelas -->
                            <td class="px-6 py-4 text-gray-700">{{ $item->kelas->kelas }}</td>
                            <!-- Tanggal -->
                            <td class="px-6 py-4 text-gray-700">{{ $item->tgl->format('d/m/Y') }}</td>
                            <!-- HB -->
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->hb }}</td>
                            <!-- Status -->
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = match($item->status) {
                                        'Normal' => 'bg-green-100 text-green-800',
                                        'Anemia' => 'bg-red-100 text-red-800',
                                        'Tinggi' => 'bg-orange-100 text-orange-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <!-- Pesan -->
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $item->pesan }}">
                                {{ $item->pesan }}
                            </td>
                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('admin.hb.edit', $item->id_hb) }}" 
                                       class="text-[#308a34] hover:text-[#1B5E20] transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.hb.destroy', $item->id_hb) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus data HB ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-tint text-gray-300 text-5xl mb-3"></i>
                                    <p class="text-gray-600">Belum ada data HB</p>
                                    <a href="{{ route('admin.hb.create') }}" 
                                       class="mt-2 inline-block text-[#308a34] hover:text-[#1B5E20] text-sm">
                                        Tambah data HB pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $hb->links() }}
        </div>
    </div>
</div>
@endsection
