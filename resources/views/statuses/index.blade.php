@extends('layouts.app')

@section('title', 'Columns')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h1 class="text-lg font-semibold mb-1">Progress Indicators (Board Columns)</h1>
        <p class="text-sm text-slate-500 mb-4">These are the stages a ticket moves through, e.g. To Do → In Progress → Checking → Done. Add as many as you need.</p>

        <form method="POST" action="{{ route('statuses.store') }}" class="flex items-end gap-3 mb-6">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-medium text-slate-500 mb-1">Name</label>
                <input type="text" name="name" required placeholder="e.g. Waiting for Parts" class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Color</label>
                <input type="color" name="color" value="#6B7280" class="h-9 w-14 rounded-md border-slate-300">
            </div>
            <button class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">Add Column</button>
        </form>

        <div class="space-y-2">
            @foreach ($statuses as $status)
                <div class="flex items-center justify-between border border-slate-200 rounded-lg px-4 py-2.5">
                    <form method="POST" action="{{ route('statuses.update', $status) }}" class="flex items-center gap-3 flex-1">
                        @csrf @method('PUT')
                        <input type="color" name="color" value="{{ $status->color }}" onchange="this.form.submit()" class="h-7 w-10 rounded border-slate-300">
                        <input type="text" name="name" value="{{ $status->name }}" onblur="if(this.value !== '{{ $status->name }}') this.form.submit()"
                               class="border-0 focus:ring-1 focus:ring-emerald-400 rounded text-sm font-medium bg-transparent flex-1">
                    </form>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span>{{ $status->tickets_count }} ticket(s)</span>
                        <form method="POST" action="{{ route('statuses.destroy', $status) }}" onsubmit="return confirm('Delete this column?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if ($statuses->isEmpty())
                <p class="text-sm text-slate-400 text-center py-6">No columns yet — add your first one above.</p>
            @endif
        </div>
    </div>
</div>
@endsection
