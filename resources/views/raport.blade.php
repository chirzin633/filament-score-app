<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Raport - {{ $student->name }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            padding: 5px 10px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
        }
        .score-table th {
            background: #6366f1;
            color: white;
            padding: 10px;
        }
        .score-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .score-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .status-lulus {
            color: green;
            font-weight: bold;
        }
        .status-remedial {
            color: red;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN HASIL BELAJAR SISWA</h2>
        <p>Tahun Ajaran 2025/2026 - Semester Ganjil</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Nama Lengkap</td>
            <td>: {{ $student->name }}</td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>: {{ $student->nis }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $classRoom?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
    </table>

    <table class="score-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Nilai Akhir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($scores as $index => $score)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left">{{ $score->subject?->name ?? '-' }}</td>
                    <td>{{ $score->tugas }}</td>
                    <td>{{ $score->uts }}</td>
                    <td>{{ $score->uas }}</td>
                    <td><strong>{{ number_format($score->final_score, 2) }}</strong></td>
                    <td class="{{ $score->final_score >= 75 ? 'status-lulus' : 'status-remedial' }}">
                        {{ $score->final_score >= 75 ? 'LULUS' : 'REMEDIAL' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada nilai tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Rata-rata: <strong>{{ number_format($avgScore, 2) }}</strong></p>
        <br /><br />
        <p>..................., {{ now()->format('d M Y') }}</p>
        <br /><br />
        <p>(_______________________)</p>
        <p>Wali Kelas</p>
    </div>
</body>
</html>
