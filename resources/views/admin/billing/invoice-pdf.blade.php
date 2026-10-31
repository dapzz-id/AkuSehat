<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran {{ $trx->order_id }}</title>
    <style>
        @page {
            margin: 12px 14px 14px 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.35;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* ─── PEMISAH GARIS PUTUS-PUTUS (KHAS ALFAMART/INDOMARET) ─── */
        .divider {
            border-top: 1px dashed #222222;
            margin: 6px 0;
            width: 100%;
        }

        .divider-double {
            border-top: 1px dashed #222222;
            margin: 5px 0 2px 0;
        }

        /* ─── HEADER STRUK ─── */
        .header {
            text-align: center;
            padding-bottom: 2px;
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 4px;
        }

        .logo-img {
            max-height: 34px;
            max-width: 130px;
            margin: 0 auto;
            display: block;
        }

        .store-name {
            font-size: 10.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .store-company {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 1px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .store-sub {
            font-size: 7.2pt;
            margin-top: 1px;
            color: #222222;
        }

        .store-address {
            font-size: 7.2pt;
            margin-top: 2px;
            line-height: 1.3;
            color: #222222;
        }

        .store-meta {
            font-size: 7pt;
            margin-top: 1px;
            color: #333333;
        }

        /* ─── TABEL METADATA TRANSAKSI ─── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin: 2px 0;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .meta-table .lbl {
            width: 32%;
            color: #333333;
        }

        .meta-table .sep {
            width: 3%;
            text-align: center;
            color: #333333;
        }

        .meta-table .val {
            width: 65%;
            color: #000000;
        }

        .meta-table .val-bold {
            width: 65%;
            font-weight: bold;
            color: #000000;
        }

        /* ─── TABEL ITEM BELANJA (FORMAT ALFAMART / INDOMARET) ─── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin: 2px 0;
        }

        .items-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .item-title {
            font-weight: bold;
            font-size: 8pt;
            color: #000000;
            text-transform: uppercase;
            padding-top: 3px !important;
        }

        .item-calc {
            padding-left: 8px !important;
            font-size: 8pt;
            color: #222222;
        }

        .item-total {
            text-align: right;
            font-size: 8pt;
            color: #000000;
            font-weight: normal;
        }

        /* ─── TABEL KALKULASI & TOTAL ─── */
        .calc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin: 2px 0;
        }

        .calc-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .c-lbl {
            width: 60%;
            color: #333333;
        }

        .c-val {
            width: 40%;
            text-align: right;
            color: #000000;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin: 2px 0;
        }

        .total-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .row-total .t-lbl {
            font-size: 9.5pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.3px;
        }

        .row-total .t-val {
            font-size: 9.5pt;
            font-weight: bold;
            text-align: right;
            color: #000000;
        }

        .row-pay .t-lbl {
            font-size: 8.5pt;
            font-weight: bold;
            color: #000000;
            padding-top: 3px;
        }

        .row-pay .t-val {
            font-size: 8.5pt;
            font-weight: bold;
            text-align: right;
            color: #000000;
            padding-top: 3px;
        }

        .row-change .t-lbl {
            font-size: 8pt;
            color: #333333;
        }

        .row-change .t-val {
            font-size: 8pt;
            text-align: right;
            color: #000000;
        }

        /* ─── STATUS LUNAS ─── */
        .paid-stamp {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #000000;
            margin: 4px 0 2px 0;
        }

        /* ─── INFORMASI EDC / DIGITAL PAYMENT ─── */
        .edc-header {
            text-align: center;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            margin: 2px 0 3px 0;
            color: #222222;
        }

        .edc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin: 1px 0;
        }

        .edc-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .e-lbl {
            width: 32%;
            color: #444444;
        }

        .e-sep {
            width: 3%;
            text-align: center;
            color: #444444;
        }

        .e-val {
            width: 65%;
            color: #000000;
        }

        /* ─── BARCODE (STRIP KASIR KHAS ALFAMART/INDOMARET) ─── */
        .barcode-container {
            text-align: center;
            margin: 7px 0 3px 0;
        }

        .barcode-lines {
            display: inline-block;
            height: 24px;
            vertical-align: middle;
        }

        .b-bar {
            display: inline-block;
            height: 24px;
            background-color: #000000;
            vertical-align: middle;
        }

        .barcode-code {
            font-size: 7pt;
            letter-spacing: 1.2px;
            margin-top: 3px;
            color: #222222;
            font-family: 'Courier New', monospace;
        }

        /* ─── FOOTER STRUK ─── */
        .footer-block {
            text-align: center;
            margin-top: 4px;
        }

        .footer-title {
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: #000000;
        }

        .footer-sub {
            font-size: 7.2pt;
            font-weight: bold;
            color: #222222;
            margin-top: 3px;
        }

        .footer-contact {
            font-size: 7pt;
            line-height: 1.35;
            color: #222222;
            margin-top: 1px;
        }

        .footer-legal {
            font-size: 6.8pt;
            line-height: 1.35;
            color: #444444;
            margin-top: 4px;
        }

        .footer-time {
            font-size: 6.8pt;
            color: #555555;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════════ -->
<!--   HEADER STRUK (ALFAMART / INDOMARET)      -->
<!-- ══════════════════════════════════════════ -->
<div class="header">
    @if(!empty($logoBase64))
    <div class="logo-wrap">
        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Aku Sehat" class="logo-img">
    </div>
    @endif
    <div class="store-name">AKU SEHAT</div>
    <div class="store-company">NEURA CAKRAWIRA SOLUSI</div>
    <div class="store-sub">PLATFORM KESEHATAN DIGITAL</div>
    <div class="store-address">
        Taman Puri Cendana A4/10, Tambun Selatan.
    </div>
    <div class="store-meta">
        NPWP: 3201071211070001 &bull; WhatsApp: +62 895-3831-07479
    </div>
    <div class="store-meta">
        Website: www.akusehat.neuracakrawira.asia
    </div>
</div>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   METADATA TRANSAKSI                       -->
<!-- ══════════════════════════════════════════ -->
<table class="meta-table">
    <tr>
        <td class="lbl">Tgl / Waktu</td>
        <td class="sep">:</td>
        <td class="val">{{ $trx->created_at->format('d/m/Y H:i:s') }} WIB</td>
    </tr>
    <tr>
        <td class="lbl">No. Struk</td>
        <td class="sep">:</td>
        <td class="val-bold">{{ $trx->order_id }}</td>
    </tr>
    <tr>
        <td class="lbl">{{ $tipeSebutan ?? ($instansi->tipe_sebutan ?? 'Institusi') }}</td>
        <td class="sep">:</td>
        <td class="val">{{ $instansi->nama_instansi ?? ($instansi->nama_sekolah ?? 'Pelanggan') }}</td>
    </tr>
    @if(!empty($instansi->npsn))
    <tr>
        <td class="lbl">NPSN</td>
        <td class="sep">:</td>
        <td class="val">{{ $instansi->npsn }}</td>
    </tr>
    @endif
    <tr>
        <td class="lbl">Pelanggan</td>
        <td class="sep">:</td>
        <td class="val">{{ $user->nama ?? $user->name ?? ($labelAdmin ?? 'Admin') }}</td>
    </tr>
</table>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   DAFTAR ITEM BELANJA                      -->
<!--   (Format Khas Alfamart / Indomaret:       -->
<!--    Baris 1: Nama Item                      -->
<!--    Baris 2: Qty x Harga Satuan & Subtotal) -->
<!-- ══════════════════════════════════════════ -->
<table class="items-table">
    @if($trx->tipe_transaksi === 'perpanjang_lisensi')
        @php
            $kuotaTrx = $trx->payload['kuota_saat_perpanjang'] ?? ($instansi->getActiveLicense()->kuota_pengguna ?? 0);
            if ($kuotaTrx <= 0 && $trx->nominal > 0) {
                $kuotaTrx = (int) round($trx->nominal / 1.11 / 12000);
            }
            $itemSubtotal = $trx->payload['subtotal'] ?? ($kuotaTrx * 12000);
            $itemCount = 1;
        @endphp
        <tr>
            <td colspan="2" class="item-title">PERPANJANGAN LISENSI {{ strtoupper($tipeSebutan ?? ($instansi->tipe_sebutan ?? 'INSTITUSI')) }} (1 TAHUN)</td>
        </tr>
        <tr>
            <td class="item-calc">&nbsp;&nbsp;{{ $kuotaTrx }} {{ $labelMember ?? ($instansi->label_member_singular ?? 'Siswa') }} &times; 12.000 / Thn</td>
            <td class="item-total">{{ number_format($itemSubtotal, 0, ',', '.') }}</td>
        </tr>
    @elseif($trx->tipe_transaksi === 'tambah_kuota')
        @php
            $itemSubtotal = $trx->payload['subtotal'] ?? ($trx->tambah_kuota * 12000);
            $itemCount = 1;
        @endphp
        <tr>
            <td colspan="2" class="item-title">TAMBAH KUOTA LISENSI {{ strtoupper($tipeSebutan ?? ($instansi->tipe_sebutan ?? 'INSTITUSI')) }}</td>
        </tr>
        <tr>
            <td class="item-calc">&nbsp;&nbsp;+{{ $trx->tambah_kuota }} {{ $labelMember ?? ($instansi->label_member_singular ?? 'Siswa') }} &times; 12.000 (1 Thn)</td>
            <td class="item-total">{{ number_format($itemSubtotal, 0, ',', '.') }}</td>
        </tr>
    @elseif($trx->tipe_transaksi === 'lisensi_tahunan')
        @php
            $itemSubtotal = $trx->tambah_kuota * 12000;
            $itemCount = 1;
        @endphp
        <tr>
            <td colspan="2" class="item-title">LISENSI {{ strtoupper($tipeSebutan ?? ($instansi->tipe_sebutan ?? 'INSTITUSI')) }} TAHUNAN</td>
        </tr>
        <tr>
            <td class="item-calc">&nbsp;&nbsp;{{ $trx->tambah_kuota }} {{ $labelMember ?? ($instansi->label_member_singular ?? 'Siswa') }} &times; 12.000 (1 Thn)</td>
            <td class="item-total">{{ number_format($trx->tambah_kuota * 12000, 0, ',', '.') }}</td>
        </tr>
    @else
        @php
            $itemCount = ($trx->durasi_bulan > 0 ? 1 : 0) + ($trx->tambah_kuota > 0 ? 1 : 0);
            if ($itemCount == 0) $itemCount = 1;
            $itemSubtotal = ($trx->durasi_bulan * 120000) + ($trx->tambah_kuota * 10000);
        @endphp
        @if($trx->durasi_bulan > 0)
        <tr>
            <td colspan="2" class="item-title">PERPANJANGAN LAYANAN</td>
        </tr>
        <tr>
            <td class="item-calc">&nbsp;&nbsp;{{ $trx->durasi_bulan }} Bln &times; 120.000</td>
            <td class="item-total">{{ number_format($trx->durasi_bulan * 120000, 0, ',', '.') }}</td>
        </tr>
        @endif

        @if($trx->tambah_kuota > 0)
        <tr>
            <td colspan="2" class="item-title">TAMBAH KUOTA ANGGOTA {{ strtoupper($labelMember ?? 'SISWA') }}</td>
        </tr>
        <tr>
            <td class="item-calc">&nbsp;&nbsp;{{ $trx->tambah_kuota }} Usr &times; 10.000</td>
            <td class="item-total">{{ number_format($trx->tambah_kuota * 10000, 0, ',', '.') }}</td>
        </tr>
        @endif

        @if($trx->durasi_bulan == 0 && $trx->tambah_kuota == 0)
        <tr>
            <td colspan="2" class="item-title">PAKET LAYANAN TERPADU AKU SEHAT</td>
        </tr>
        <tr>
            <td class="item-calc">&nbsp;&nbsp;1 Pkt &times; {{ number_format($trx->nominal, 0, ',', '.') }}</td>
            <td class="item-total">{{ number_format($trx->nominal, 0, ',', '.') }}</td>
        </tr>
        @endif
    @endif
</table>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   SUBTOTAL & KALKULASI PAJAK (11%)         -->
<!-- ══════════════════════════════════════════ -->
@php
    if (!isset($itemSubtotal) || $itemSubtotal <= 0) {
        $itemSubtotal = (int) round($trx->nominal / 1.11);
    }
    $ppn11 = (int) round($itemSubtotal * 0.11);
    $totalBelanja = $itemSubtotal + $ppn11;
@endphp

<table class="calc-table">
    <tr>
        <td class="c-lbl">Total Item ({{ $itemCount }})</td>
        <td class="c-val">{{ number_format($itemSubtotal, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="c-lbl">Diskon Promo</td>
        <td class="c-val">0</td>
    </tr>
    <tr>
        <td class="c-lbl">PPN (11%)</td>
        <td class="c-val">{{ number_format($ppn11, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="c-lbl">Biaya Transaksi</td>
        <td class="c-val">0</td>
    </tr>
</table>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   TOTAL BELANJA & PEMBAYARAN               -->
<!-- ══════════════════════════════════════════ -->
<table class="total-table">
    <tr class="row-total">
        <td class="t-lbl">TOTAL BELANJA</td>
        <td class="t-val">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</td>
    </tr>
    <tr class="row-pay">
        <td class="t-lbl">BAYAR ({{ $trx->payment_method === 'google_pay' ? 'GOOGLE PAY' : strtoupper($trx->payment_method) }})</td>
        <td class="t-val">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</td>
    </tr>
    <tr class="row-change">
        <td class="t-lbl">KEMBALIAN</td>
        <td class="t-val">Rp 0</td>
    </tr>
</table>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   STATUS TRANSAKSI                         -->
<!-- ══════════════════════════════════════════ -->
<div class="paid-stamp">*** LUNAS / BERHASIL ***</div>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   STRIP TRANSAKSI NON-TUNAI (EDC/E-WALLET) -->
<!-- ══════════════════════════════════════════ -->
<div class="edc-header">-- TRANSAKSI NON TUNAI --</div>
<table class="edc-table">
    <tr>
        <td class="e-lbl">Metode</td>
        <td class="e-sep">:</td>
        <td class="e-val">{{ $trx->payment_method === 'google_pay' ? 'GOOGLE PAY (IN-APP)' : strtoupper($trx->payment_method) }}</td>
    </tr>
    <tr>
        <td class="e-lbl">Reff No</td>
        <td class="e-sep">:</td>
        <td class="e-val">{{ $trx->order_id }}</td>
    </tr>
    <tr>
        <td class="e-lbl">Auth Code</td>
        <td class="e-sep">:</td>
        <td class="e-val">{{ substr($trx->payment_token ?? ('AUTH_' . $trx->id), 0, 24) }}</td>
    </tr>
    <tr>
        <td class="e-lbl">Status</td>
        <td class="e-sep">:</td>
        <td class="e-val"><strong>APPROVED / SUCCESS</strong></td>
    </tr>
</table>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   BARCODE KASIR                            -->
<!-- ══════════════════════════════════════════ -->
<div class="barcode-container">
    @php
        $bars = [2,1,3,1.5,2,1,3,1,2.5,1.5,3,1,2,1.5,3,1,1.5,2.5,3,1,1.5,2,3,1,2,1,1.5,3];
    @endphp
    <div class="barcode-lines">
        @foreach($bars as $i => $w)
            <span class="b-bar" style="width: {{ $w }}px; margin-right: {{ ($i % 3 === 2) ? '2' : '0.8' }}px;"></span>
        @endforeach
    </div>
    <div class="barcode-code">* {{ $trx->order_id }} *</div>
</div>

<div class="divider"></div>

<!-- ══════════════════════════════════════════ -->
<!--   FOOTER STRUK (ALFAMART / INDOMARET)      -->
<!-- ══════════════════════════════════════════ -->
<div class="footer-block">
    <div class="footer-title">TERIMA KASIH ATAS KUNJUNGAN ANDA</div>
    <div class="footer-sub">LAYANAN KONSUMEN AKU SEHAT:</div>
    <div class="footer-contact">
        SMS / WA : +62 895-3831-07479<br>
        Email : support@akusehat.neuracakrawira.asia<br>
        Website : www.akusehat.neuracakrawira.asia
    </div>
    <div class="divider" style="margin: 5px 0;"></div>
    <div class="footer-legal">
        Struk ini merupakan bukti transaksi elektronik yang sah.<br>
        Masa aktif lisensi &amp; kuota diperbarui secara real-time.<br>
        Dapat digunakan sebagai lampiran LPJ Dana BOS Instansi.
    </div>
    <div class="footer-time">
        Waktu Cetak: {{ now()->format('d/m/Y H:i:s') }} WIB
    </div>
</div>

</body>
</html>
