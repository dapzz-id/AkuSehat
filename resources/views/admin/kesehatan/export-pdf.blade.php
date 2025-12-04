<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Data Kesehatan - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        hr {
            border: 1.5px solid black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-container table {
            border: none !important;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #e8f5e9;
            color: #1b5e20;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            padding: 6px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-bmi-normal {
            background-color: #c8e6c9;
            color: #1b5e20;
        }

        .badge-bmi-underweight {
            background-color: #fff9c4;
            color: #f57f17;
        }

        .badge-bmi-overweight {
            background-color: #ffe0b2;
            color: #ef6c00;
        }

        .badge-bmi-obesitas-level-1 {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .badge-bmi-obesitas-level-2 {
            background-color: #f44336;
            color: #ffffff;
        }

        .badge-bmi-obesitas-level-3 {
            background-color: #b71c1c;
            color: #ffffff;
        }

        .badge-tkd-normal {
            background-color: #c8e6c9;
            color: #1b5e20;
        }

        .badge-tkd-elevasi {
            background-color: #fff9c4;
            color: #f57f17;
        }

        .badge-tkd-hipertensi-tahap-1 {
            background-color: #ffe0b2;
            color: #ef6c00;
        }

        .badge-tkd-hipertensi-tahap-2 {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .badge-tkd-krisis {
            background-color: #b71c1c;
            color: #ffffff;
        }

        .badge-tkd-hipotensi {
            background-color: #bbdefb;
            color: #0d47a1;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <!-- KOP SURAT -->
    <div class="kop-container" style="margin-bottom: 10px;">
        <table style="width:100%; border:none; border-collapse:collapse;">
            <tr>
                <!-- Logo Kiri -->
                <td style="width:120px; text-align:center; border:none;">
                    <img src="{{ $raadeveloperz_cr }}" style="width:90px; height:auto;">
                </td>

                <!-- Teks Kop -->
                <td style="text-align:center; border:none; line-height:1.3;">
                    <div style="font-size:22px; font-weight:bold;">RADEVELOPERZ</div>
                    <div style="font-size:14px; font-weight:bold;">
                        Innovative Digital Solutions for Modern Challenges
                    </div>
                    <div style="font-size:12px;">
                        Taman Puri Cendana, Grand Mawar, Blok A4 no 10<br>
                        Tambun Selatan, Kabupaten Bekasi, 17510
                    </div>
                    <div style="font-size:12px; margin-top:3px;">
                        WhatsApp: +62895383107479 |
                        Email: raadeveloperz@gmail.com<br>
                        Website: www.raadeveloperz.web.id
                    </div>
                </td>

                <!-- Logo Kanan -->
                <td style="width:120px; text-align:center; border:none;">
                    <img src="{{ $ikonPath }}" style="width:90px; height:auto;">
                </td>
            </tr>
        </table>

        <hr>
    </div>

    <!-- Subjudul PDF -->
    <div style="text-align:center; margin-top: 0px; margin-bottom: 4px;">
        <h3 style="margin: 0;">DATA KESEHATAN {{ $namaInstansi }}</h3>
        <p style="margin: 0; font-size: 11px;">Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- TABEL DATA -->
    <table style="margin-top: 10px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Member</th>
                <th>Nomor Induk</th>
                <th>Divisi</th>
                <th>Tanggal</th>
                <th>BB (kg)</th>
                <th>TB (cm)</th>
                <th>IMT</th>
                <th>Status BMI</th>
                <th>Tekanan Darah</th>
                <th>Status Tekanan Darah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kesehatan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ $item->user->nomor_induk }}</td>
                    <td>{{ $item->user->divisi->divisi_name ?? '-' }}</td>
                    <td>{{ $item->tgl->format('d/m/Y') }}</td>
                    <td>{{ $item->bb }}</td>
                    <td>{{ $item->tb }}</td>
                    <td>{{ $item->imt }}</td>
                    <td style="text-align:center;">
                        <span class="badge badge-bmi-{{ str_replace(' ', '-', strtolower($item->status)) }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td>{{ $item->sistol }}/{{ $item->diastol }}</td>
                    <td style="text-align:center;">
                        <span class="badge badge-tkd-{{ str_replace(' ', '-', strtolower($item->status_darah)) }}">
                            {{ $item->status_darah }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Data: {{ $kesehatan->count() }}
    </div>

</body>
</html>