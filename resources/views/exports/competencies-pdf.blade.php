<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Kompetensi</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #059669; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #059669; margin: 0 0 4px; }
        .header p { font-size: 11px; color: #6b7280; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #059669; color: #ffffff; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-active { background-color: #dcfce7; color: #166534; }
        .badge-inactive { background-color: #f3f4f6; color: #374151; }
        .footer { text-align: right; margin-top: 15px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Boilerplate') }}</h1>
        <p>Laporan Data Kompetensi &mdash; {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">Kode</th>
                <th style="width: 25%;">Nama Kompetensi</th>
                <th style="width: 15%;">Level</th>
                <th style="width: 36%;">Deskripsi</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competencies as $index => $competency)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $competency->code }}</strong></td>
                    <td>{{ $competency->name }}</td>
                    <td>{{ $competency->level_label }}</td>
                    <td>{{ Str::limit($competency->description ?? '-', 80) }}</td>
                    <td>
                        <span class="badge {{ $competency->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $competency->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} &mdash; {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
