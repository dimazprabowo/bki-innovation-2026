<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Project Pengadaan</title>
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
        .badge-draft { background-color: #f3f4f6; color: #374151; }
        .badge-submitted { background-color: #dbeafe; color: #1e40af; }
        .badge-coe { background-color: #e9d5ff; color: #6b21a8; }
        .badge-approved { background-color: #dcfce7; color: #166534; }
        .badge-progress { background-color: #fef3c7; color: #92400e; }
        .badge-completed { background-color: #d1fae5; color: #065f46; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; }
        .footer { text-align: right; margin-top: 15px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Boilerplate') }}</h1>
        <p>Laporan Data Project Pengadaan &mdash; {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 20%;">Nama Project</th>
                <th style="width: 15%;">Scope</th>
                <th style="width: 8%;">Durasi</th>
                <th style="width: 8%;">Risk</th>
                <th style="width: 10%;">CoE</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 5%;">Modul</th>
                <th style="width: 12%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $index => $project)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $project->code }}</strong></td>
                    <td>{{ $project->name }}</td>
                    <td>{{ Str::limit($project->scope ?? '-', 50) }}</td>
                    <td>{{ $project->duration ?? '-' }}</td>
                    <td>
                        @php
                            $riskBadge = match($project->risk_level->value) {
                                'low' => 'badge-low',
                                'medium' => 'badge-medium',
                                'high' => 'badge-high',
                                default => 'badge-low',
                            };
                        @endphp
                        <span class="badge {{ $riskBadge }}">
                            {{ $project->risk_level->label() }}
                        </span>
                    </td>
                    <td>{{ $project->coe_control_level->label() }}</td>
                    <td>
                        @php
                            $statusBadge = match($project->status) {
                                'draft' => 'badge-draft',
                                'submitted' => 'badge-submitted',
                                'coe_review' => 'badge-coe',
                                'approved' => 'badge-approved',
                                'in_progress' => 'badge-progress',
                                'completed' => 'badge-completed',
                                'cancelled' => 'badge-cancelled',
                                default => 'badge-draft',
                            };
                            $statuses = [
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'coe_review' => 'CoE Review',
                                'approved' => 'Approved',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ];
                        @endphp
                        <span class="badge {{ $statusBadge }}">
                            {{ $statuses[$project->status] ?? $project->status }}
                        </span>
                    </td>
                    <td>{{ $project->modules_count }}</td>
                    <td>{{ $project->total_estimate ? 'Rp ' . number_format($project->total_estimate, 0, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} &mdash; {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
