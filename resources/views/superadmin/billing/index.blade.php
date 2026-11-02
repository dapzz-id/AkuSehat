@extends('layouts.app')

@section('title', 'Manajemen Billing & Transaksi - SuperAdmin')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-receipt text-emerald-600"></i>
                <span>Manajemen Billing & Transaksi</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola dan verifikasi pembayaran manual transfer BCA, aktivasi lisensi instansi, dan pantau seluruh transaksi aplikasi.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-xs text-xs sm:text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-key text-amber-500"></i>
                <span>Daftar Lisensi Aktif</span>
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Statistic Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Pendapatan --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pendapatan</span>
                <div class="text-xl font-black text-gray-900 mt-1">
                    Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                    <i class="fas fa-circle-check"></i> Transaksi Lunas
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-sack-dollar"></i>
            </div>
        </div>

        {{-- Card 2: Menunggu Verifikasi --}}
        <div class="bg-white p-5 rounded-2xl border {{ $stats['pending_count'] > 0 ? 'border-amber-300 ring-2 ring-amber-500/20' : 'border-gray-100' }} shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Menunggu Verifikasi</span>
                <div class="text-xl font-black text-amber-600 mt-1">
                    {{ $stats['pending_count'] }} Pesanan
                </div>
                <span class="text-[11px] {{ $stats['pending_count'] > 0 ? 'text-amber-700 font-bold' : 'text-gray-400' }} mt-0.5 block">
                    {{ $stats['pending_count'] > 0 ? 'Perlu tindakan SuperAdmin' : 'Semua transaksi terverifikasi' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>

        {{-- Card 3: Transaksi Berhasil --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Berhasil / Lunas</span>
                <div class="text-xl font-black text-gray-900 mt-1">
                    {{ $stats['success_count'] }}
                </div>
                <span class="text-[11px] text-gray-500 mt-0.5 block">
                    Lisensi telah aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>

        {{-- Card 4: Total Transaksi --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Semua Order</span>
                <div class="text-xl font-black text-gray-900 mt-1">
                    {{ $stats['total_count'] }}
                </div>
                <span class="text-[11px] text-gray-500 mt-0.5 block">
                    Sejak sistem beroperasi
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Main Table -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
        {{-- Tabs Status Filter --}}
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('superadmin.billing.index') }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($currentStatus) || $currentStatus === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua ({{ $stats['total_count'] }})
                </a>
                <a href="{{ route('superadmin.billing.index', ['status' => 'pending']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $currentStatus === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    <span>Menunggu Verifikasi</span>
                    @if($stats['pending_count'] > 0)
                        <span class="w-5 h-5 rounded-full {{ $currentStatus === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-500 text-white' }} text-[10px] font-black flex items-center justify-center">
                            {{ $stats['pending_count'] }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('superadmin.billing.index', ['status' => 'success']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currentStatus === 'success' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Berhasil / Lunas ({{ $stats['success_count'] }})
                </a>
                <a href="{{ route('superadmin.billing.index', ['status' => 'failed']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currentStatus === 'failed' ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Gagal / Dibatalkan
                </a>
            </div>

            <div class="text-xs text-gray-400 font-medium">
                Menampilkan halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-[11px] font-bold uppercase tracking-wider text-left">
                        <th class="px-5 py-3.5">Order ID & Waktu</th>
                        <th class="px-5 py-3.5">Instansi / Sekolah</th>
                        <th class="px-5 py-3.5">Rincian Paket</th>
                        <th class="px-5 py-3.5">Total Nominal</th>
                        <th class="px-5 py-3.5">Metode</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                        @php
                            $payload = is_array($trx->payload) ? $trx->payload : [];
                            $isPending = $trx->status === 'pending';
                            $isSuccess = in_array($trx->status, ['success', 'paid']);
                            $isFailed = in_array($trx->status, ['failed', 'expired']);
                            $isBca = str_contains(strtolower($trx->payment_method ?? ''), 'bca') || str_contains(strtolower($trx->payment_method ?? ''), 'manual');
                            $namaInstansi = $trx->instansi ? ($trx->instansi->nama_instansi ?? $trx->instansi->nama_sekolah ?? 'Instansi') : 'Instansi';
                            $deskripsiItem = $payload['deskripsi_item'] ?? ($trx->tipe_transaksi === 'perpanjang_lisensi' ? 'Perpanjangan Lisensi' : 'Tambah Paket Kuota');
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors {{ $isPending ? 'bg-amber-50/30' : '' }}">
                            {{-- Order ID & Waktu --}}
                            <td class="px-5 py-4">
                                <div class="font-mono font-bold text-gray-900 text-xs">
                                    {{ $trx->order_id }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </td>

                            {{-- Instansi / Sekolah --}}
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800 text-sm">
                                    {{ $namaInstansi }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                    <span>Pemesan: <strong>{{ $trx->user ? $trx->user->name : '-' }}</strong></span>
                                </div>
                            </td>

                            {{-- Rincian Paket --}}
                            <td class="px-5 py-4">
                                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $trx->tipe_transaksi === 'perpanjang_lisensi' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    <i class="fas {{ $trx->tipe_transaksi === 'perpanjang_lisensi' ? 'fa-rotate' : 'fa-user-plus' }} text-[10px]"></i>
                                    <span>{{ $deskripsiItem }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Durasi: <strong>{{ $trx->durasi_bulan ?? 1 }} Bulan</strong>
                                    @if($trx->tambah_kuota > 0)
                                        • +{{ $trx->tambah_kuota }} User
                                    @endif
                                </div>
                            </td>

                            {{-- Total Nominal --}}
                            <td class="px-5 py-4">
                                <span class="font-black text-gray-900 text-sm whitespace-nowrap">
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </span>
                                <div class="text-[10px] text-gray-400">Termasuk PPN 11%</div>
                            </td>

                            {{-- Metode Pembayaran --}}
                            <td class="px-5 py-4">
                                @if($isBca)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-[#0c2e68] text-white">
                                        <i class="fas fa-building-columns text-[10px]"></i> Transfer BCA
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-neutral-900 text-white">
                                        <i class="fab fa-google-pay text-sm"></i> Google Pay
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                @if($isPending)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                        <i class="fas fa-hourglass-half text-[10px]"></i> Menunggu Verifikasi
                                    </span>
                                @elseif($isSuccess)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-circle-check text-[10px]"></i> Berhasil / Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <i class="fas fa-circle-xmark text-[10px]"></i> Gagal / Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Tombol Struk PDF --}}
                                    <a href="{{ route('superadmin.billing.invoice.pdf', $trx->id) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors flex items-center gap-1"
                                       title="Buka Struk Resmi PDF">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="hidden xl:inline">Struk</span>
                                    </a>

                                    @if($isPending)
                                        {{-- Tombol Setujui / Verifikasi --}}
                                        <button type="button" 
                                                onclick="openApproveModal('{{ $trx->id }}', '{{ $trx->order_id }}', '{{ addslashes($namaInstansi) }}', 'Rp {{ number_format($trx->nominal, 0, ',', '.') }}', '{{ addslashes($deskripsiItem) }}')"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                                            <i class="fas fa-check"></i>
                                            <span>Verifikasi</span>
                                        </button>

                                        {{-- Tombol Tolak --}}
                                        <button type="button" 
                                                onclick="openRejectModal('{{ $trx->id }}', '{{ $trx->order_id }}')"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 active:scale-95 transition-all cursor-pointer"
                                                title="Tolak Pesanan">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm font-medium">Belum ada transaksi ditemukan untuk filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="p-4 sm:p-5 border-t border-gray-100">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL KONFIRMASI APPROVE / VERIFIKASI PEMBAYARAN --}}
<div id="approveModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150">
        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Verifikasi Pembayaran BCA</h3>
                <p class="text-xs text-gray-500">Konfirmasi penerimaan dana transfer</p>
            </div>
        </div>

        <p class="text-xs text-gray-600 leading-relaxed">
            Apakah Anda yakin telah mengecek mutasi rekening BCA dan dana dari pesanan berikut sudah benar-benar masuk?
        </p>

        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 text-xs space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-500">Order ID:</span>
                <span class="font-mono font-bold text-gray-900" id="approveModalOrderId">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Instansi:</span>
                <span class="font-bold text-gray-800" id="approveModalInstansi">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Rincian Paket:</span>
                <span class="font-bold text-emerald-700" id="approveModalDeskripsi">-</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-gray-200">
                <span class="text-gray-600 font-bold">Total Diterima:</span>
                <span class="font-extrabold text-emerald-800 text-sm" id="approveModalTotal">-</span>
            </div>
        </div>

        <div class="bg-emerald-50/70 p-3 rounded-xl border border-emerald-200 text-[11px] text-emerald-900 leading-relaxed">
            <i class="fas fa-info-circle text-emerald-600 mr-1"></i>
            Setelah disetujui, sistem akan <strong>otomatis memperpanjang masa aktif lisensi</strong> atau <strong>membuat paket kuota baru</strong> untuk instansi tersebut tanpa perlu tindakan manual lainnya.
        </div>

        <form id="approveForm" method="POST" action="">
            @csrf
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitApprove"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Ya, Verifikasi & Aktifkan Paket</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TOLAK / BATALKAN PESANAN --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150">
        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Tolak Pesanan Transfer</h3>
                <p class="text-xs text-gray-500">Batalkan pesanan yang tidak valid</p>
            </div>
        </div>

        <p class="text-xs text-gray-600 leading-relaxed">
            Pesanan <strong id="rejectModalOrderId" class="font-mono"></strong> akan ditandai sebagai gagal. Masukkan alasan penolakan jika diperlukan:
        </p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div>
                <label for="rejectReason" class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Penolakan</label>
                <textarea id="rejectReason" name="reason" rows="3"
                          placeholder="Misal: Bukti transfer palsu, dana tidak masuk mutasi BCA, nominal tidak sesuai..."
                          class="w-full p-3 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:border-transparent"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-ban"></i>
                    <span>Tolak Pesanan Ini</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id, orderId, instansi, total, deskripsi) {
        document.getElementById('approveModalOrderId').textContent = orderId;
        document.getElementById('approveModalInstansi').textContent = instansi;
        document.getElementById('approveModalTotal').textContent = total;
        document.getElementById('approveModalDeskripsi').textContent = deskripsi;
        
        const form = document.getElementById('approveForm');
        form.action = "{{ url('superadmin/billing/transactions') }}/" + id + "/approve";
        
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function openRejectModal(id, orderId) {
        document.getElementById('rejectModalOrderId').textContent = orderId;
        
        const form = document.getElementById('rejectForm');
        form.action = "{{ url('superadmin/billing/transactions') }}/" + id + "/reject";
        
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Close on click outside
    window.addEventListener('click', function(e) {
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        if (e.target === approveModal) closeApproveModal();
        if (e.target === rejectModal) closeRejectModal();
    });
</script>
@endsection
