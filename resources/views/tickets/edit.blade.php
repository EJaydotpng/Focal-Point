@extends('layouts.app')

@section('title', 'Edit Ticket')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-slate-200 p-6">
    <h1 class="text-lg font-semibold mb-4">Edit Ticket #{{ $ticket->id }}</h1>

    <form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $ticket->title) }}" required class="w-full rounded-md border-slate-300">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full rounded-md border-slate-300">{{ old('description', $ticket->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Column / Status</label>
                <select name="status_id" required class="w-full rounded-md border-slate-300">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" {{ old('status_id', $ticket->status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Priority</label>
                <select name="priority" class="w-full rounded-md border-slate-300">
                    @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $ticket->priority) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Reporter</label>
                <input type="text" name="reporter" value="{{ old('reporter', $ticket->reporter) }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Assignee</label>
                <input type="text" name="assignee" value="{{ old('assignee', $ticket->assignee) }}" class="w-full rounded-md border-slate-300">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Due date</label>
            <input type="date" name="due_date" value="{{ old('due_date', optional($ticket->due_date)->format('Y-m-d')) }}" class="rounded-md border-slate-300">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Categories</label>
            <div class="flex flex-wrap gap-3">
                @foreach ($categories as $cat)
                    <label class="inline-flex items-center gap-1.5 text-sm bg-slate-50 border border-slate-200 rounded-full px-3 py-1 cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                               {{ $ticket->categories->contains($cat->id) ? 'checked' : '' }}>
                        <span style="color: {{ $cat->color }};">{{ $cat->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Add more attachments</label>
            <input type="file" name="attachments[]" multiple accept="image/*,video/*" class="w-full text-sm border border-slate-300 rounded-md p-2">
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">Save Changes</button>
            <a href="{{ route('tickets.show', $ticket) }}" class="text-sm text-slate-500 hover:text-slate-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
