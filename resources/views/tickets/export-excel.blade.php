<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Column / Status</th>
                <th>Categories</th>
                <th>Priority</th>
                <th>Reporter</th>
                <th>Assignee</th>
                <th>Due Date</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>{{ $ticket->description }}</td>
                    <td>{{ $ticket->status->name ?? '' }}</td>
                    <td>{{ $ticket->categories->pluck('name')->implode(', ') }}</td>
                    <td>{{ ucfirst($ticket->priority) }}</td>
                    <td>{{ $ticket->reporter }}</td>
                    <td>{{ $ticket->assignee }}</td>
                    <td>{{ $ticket->due_date ? $ticket->due_date->format('Y-m-d') : '' }}</td>
                    <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
