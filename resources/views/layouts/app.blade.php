<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Ticketing') | IT Support Desk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col">

    <nav class="bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
            <a href="{{ route('tickets.index') }}" class="font-semibold text-lg flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                IT Support Desk
            </a>
            <div class="flex gap-1 text-sm">
                <a href="{{ route('tickets.index') }}" class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('tickets.*') ? 'bg-slate-700' : '' }}">Board</a>
                <a href="{{ route('statuses.index') }}" class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('statuses.*') ? 'bg-slate-700' : '' }}">Columns</a>
                <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('categories.*') ? 'bg-slate-700' : '' }}">Categories</a>
                <a href="{{ route('tickets.create') }}" class="ml-2 px-3 py-2 rounded bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium">+ New Ticket</a>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-slate-400 py-4">Internal IT Ticketing System</footer>
</body>
</html>
