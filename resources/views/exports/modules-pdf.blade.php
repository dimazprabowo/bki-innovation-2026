<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Modul Pengadaan</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #2563eb; margin: 0 0 4px; }
        .header p { font-size: 11px; color: #6b7280; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2563eb; color: #ffffff; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-low { background-color: #dcfce7; color: #166534; }
        .badge-medium { background-color: #fef3c7; color: #92400e; }
        .badge-high { background-color: #fee2e2; color: #991b1b; }
        .badge-active { background-color: #dcfce7; color: #166534; }
        .badge-inactive { background-color: #f3f4f6; color: #374151; }
        .footer { text-align: right; margin-top: 15px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Boilerplate') }}</h1>
        <p>Laporan Data Modul Pengadaan &mdash; {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 20%;">Nama Modul</th>
                <th style="width: 15%;">Scope</th>
                <th style="width: 10%;">Durasi</th>
                <th style="width: 8%;">Risk</th>
                <th style="width: 12%;">Pricing</th>
                <th style="width: 10%;">CoE Control</th>
                <th style="width: 6%;">Projects</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modules as $index => $module)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $module->code }}</strong></td>
                    <td>{{ $module->name }}</td>
                    <td>{{ Str::limit($module->scope ?? '-', 50) }}</td>
                    <td>{{ $module->duration ?? '-' }}</td>
                    <td>
                        @php
                            $riskBadge = match($module->risk_level->value) {
                                'low' => 'badge-low',
                                'medium' => 'badge-medium',
                                'high' => 'badge-high',
                                default => 'badge-low',
                            };
                        @endphp
                        <span class="badge {{ $riskBadge }}">
                            {{ $module->risk_level->label() }}
                        </span>
                    </td>
                    <td>{{ $module->pricing_baseline ? 'Rp ' . number_format($module->pricing_baseline, 0, ',', '.') : '-' }}</td>
                    <td>{{ $module->coe_control_level->label() }}</td>
                    <td>{{ $module->projects_count }}</td>
                    <td>
                        <span class="badge {{ $module->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $module->is_active ? 'Aktif' : 'Non-Aktif' }}
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
