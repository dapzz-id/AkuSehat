<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Hemoglobin - {{ date('d/m/Y') }}</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 8px;
        }
        .header h1 {
            color: #2e7d32;
            margin: 0;
            font-size: 16px;
        }
        .header p {
            color: #666;
            margin: 3px 0 0 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #e8f5e9;
            color: #1b5e20;
            font-weight: bold;
            text-align: center;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
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
        .badge-normal { background-color: #c8e6c9; color: #1b5e20; }
        .badge-anemia { background-color: #ffcdd2; color: #c62828; }
        .badge-tinggi { background-color: #ffe0b2; color: #ef6c00; }
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
    <div class="header">
        <table align="center" style="border: none; border-collapse: collapse; width: auto; margin: 0 auto;">
            <tr>
                <td style="border: none; vertical-align: middle; text-align: right; padding-right: 5px;">
                    <img src="{{ $ikonPath }}" alt="Ikon Sehat" style="width:40px; height:40px;">
                </td>
                <td style="border: none; vertical-align: middle; text-align: left;">
                    <h1 style="color: #2e7d32; margin: 0; padding: 0;">AKU SEHAT</h1>
                </td>
            </tr>
        </table>

        <h3 style="margin-top: 0px">(DATA HEMOGLOBIN {{ $namaSekolah }})</h3>
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>JK</th>
                <th>Kelas</th>
                <th>Tanggal</th>
                <th>HB (g/dL)</th>
                <th>Status</th>
                <th>Pesan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hb as $index => $item)
            <tr>
                <td class="no">{{ $index + 1 }}</td>
                <td>{{ $item->user->nama }}</td>
                <td>{{ $item->user->nis }}</td>
                <td class="center">{{ $item->user->jk === 'L' ? 'L' : 'P' }}</td>
                <td>{{ $item->kelas->kelas ?? '-' }}</td>
                <td class="center">{{ $item->tgl->format('d/m/Y') }}</td>
                <td class="number">{{ $item->hb }}</td>
                <td class="center">
                    <span class="badge badge-{{ strtolower($item->status) }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td>{{ $item->pesan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Data: {{ $hb->count() }}
    </div>
</body>
</html>