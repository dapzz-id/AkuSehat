<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Hemoglobin Report</title>
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
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            min-width: 50px;
        }

        .badge-normal {
            background-color: #c8e6c9;
            color: #1b5e20;
        }

        .badge-anemia {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .badge-tinggi {
            background-color: #ffe0b2;
            color: #ef6c00;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            color: #666;
            font-size: 8px;
        }

        .no {
            text-align: center;
            width: 30px;
        }

        .number {
            text-align: center;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
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
                    <img src="{{ $ikonPath }}" style="width:90px; height:auto; margin-bottom:4px;">
                </td>
            </tr>
        </table>

        <hr>
    </div>

    <div style="text-align:center; margin-top: 20px; margin-bottom: 22px;">
        <h3 style="margin: 0;">DATA KESEHATAN {{ $namaInstansi }}</h3>
        <p style="margin: 0; font-size: 11px; margin-top:2px;">Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th>Nama Member</th>
                <th>Nomor Induk</th>
                <th>Jenis Kelamin</th>
                <th>Divisi</th>
                <th>Tanggal</th>
                <th>HB (g/dL)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hb as $index => $item)
                <tr>
                    <td class="no">{{ $index + 1 }}</td>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ $item->user->nomor_induk }}</td>
                    <td class="center">{{ $item->user->jk === 'L' ? 'L' : 'P' }}</td>
                    <td>{{ $item->divisi->divisi_name ?? '-' }}</td>
                    <td class="center">{{ $item->tgl->format('d/m/Y') }}</td>
                    <td class="number">{{ $item->hb }}</td>
                    <td class="center">
                        <span class="badge badge-{{ strtolower($item->status) }}">
                            {{ $item->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Data: {{ $hb->count() }}
    </div>
</body>
</html>