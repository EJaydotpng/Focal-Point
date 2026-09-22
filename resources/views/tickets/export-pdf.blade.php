<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasks Export</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 2px; }
        .meta { color: #64748b; font-size: 11px; margin-bottom: 20px; }
        h2 { font-size: 14px; margin: 18px 0 8px; padding-bottom: 4px; border-bottom: 2px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; vertical-align: top; }
        th { background: #f8fafc; color: #475569; font-weight: 600; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 10px; font-size: 10px; margin-right: 3px; }
        .empty { color: #94a3b8; font-style: italic; font-size: 11px; padding: 6px 0 16px; }
        .no-print { margin-bottom: 16px; }
        @media print {
            .no-print { display: none; }
            h2 { page-break-after: avoid; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="background:#10b981;border:none;color:#0f172a;font-weight:600;padding:8px 14px;border-radius:6px;cursor:pointer;">
            Print / Save as PDF
        </button>
    </div>

    <h1>IT Support Desk — Tasks Export</h1>
    <p class="meta">Generated {{ now()->format('M d, Y h:i A') }}</p>

    @foreach ($statuses as $status)
        <h2>{{ $status->name }} ({{ $status->tickets->count() }})</h2>

        @if ($status->tickets->isEmpty())
            <p class="empty">No tickets in this column.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:26%">Title</th>
                        <th style="width:16%">Categories</th>
                        <th style="width:10%">Priority</th>
                        <th style="width:14%">Reporter</th>
                        <th style="width:14%">Assignee</th>
                        <th style="width:10%">Due Date</th>
                        <th style="width:10%">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($status->tickets as $ticket)
                        <tr>
                            <td><strong>{{ $ticket->title }}</strong>{{ $ticket->description ? ' — ' . \Illuminate\Support\Str::limit($ticket->description, 80) : '' }}</td>
                            <td>
                                @forelse ($ticket->categories as $cat)
                                    <span class="badge" style="background: {{ $cat->color }}22; color: {{ $cat->color }};">{{ $cat->name }}</span>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td>{{ ucfirst($ticket->priority) }}</td>
                            <td>{{ $ticket->reporter ?: '—' }}</td>
                            <td>{{ $ticket->assignee ?: 'Unassigned' }}</td>
                            <td>{{ $ticket->due_date ? $ticket->due_date->format('M d, Y') : '—' }}</td>
                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <script>
        // Give the page a moment to render before opening the print dialog automatically.
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
</body>
</html>
