@extends('layouts.app')

@section('title', $ticket->title)

@section('content')
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Ticket #{{ $ticket->id }}</p>
                    <h1 class="text-xl font-semibold">{{ $ticket->title }}</h1>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('tickets.edit', $ticket) }}" class="text-sm px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Edit</a>
                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Delete this ticket and all its attachments?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1.5 rounded-md border border-red-300 text-red-600 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            </div>

            @if ($ticket->categories->count())
                <div class="flex flex-wrap gap-1 mt-3">
                    @foreach ($ticket->categories as $cat)
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: {{ $cat->color }}22; color: {{ $cat->color }};">{{ $cat->name }}</span>
                    @endforeach
                </div>
            @endif

            <p class="mt-4 text-sm text-slate-600 whitespace-pre-line">{{ $ticket->description ?: 'No description provided.' }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-medium mb-3">Attachments <span class="text-slate-400 text-sm">(problem reference & means of verification)</span></h2>

            <form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data" class="flex items-center gap-2 mb-4">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $ticket->title }}">
                <input type="hidden" name="status_id" value="{{ $ticket->status_id }}">
                <input type="hidden" name="priority" value="{{ $ticket->priority }}">
                <input type="file" name="attachments[]" multiple accept="image/*,video/*" class="text-sm border border-slate-300 rounded-md p-1.5 flex-1">
                <button class="text-sm px-3 py-1.5 rounded-md bg-slate-800 text-white hover:bg-slate-700">Upload</button>
            </form>

            @if ($ticket->attachments->isEmpty())
                <p class="text-sm text-slate-400">No files uploaded yet.</p>
            @else
                <div class="grid grid-cols-3 gap-3">
                    @foreach ($ticket->attachments as $attachment)
                        <div class="relative group border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                            @if ($attachment->type === 'image')
                                <a href="{{ $attachment->url }}" target="_blank">
                                    <img src="{{ $attachment->url }}" class="w-full h-32 object-cover">
                                </a>
                            @elseif ($attachment->type === 'video')
                                <video src="{{ $attachment->url }}" class="w-full h-32 object-cover" controls></video>
                            @else
                                <a href="{{ $attachment->url }}" target="_blank" class="flex items-center justify-center h-32 text-slate-400 text-sm">📄 {{ $attachment->original_name }}</a>
                            @endif
                            <div class="p-1.5 text-[11px] text-slate-500 truncate">{{ $attachment->original_name }}</div>
                            <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" onsubmit="return confirm('Remove this file?')"
                                  class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                                @csrf @method('DELETE')
                                <button class="bg-black/60 text-white text-xs w-6 h-6 rounded-full leading-none">✕</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-medium mb-3 text-sm text-slate-500">Details</h2>

            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-3">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $ticket->title }}">
                <input type="hidden" name="description" value="{{ $ticket->description }}">

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                    <select name="status_id" onchange="this.form.submit()" class="w-full text-sm rounded-md border-slate-300">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="priority" value="{{ $ticket->priority }}">
                <input type="hidden" name="reporter" value="{{ $ticket->reporter }}">
                <input type="hidden" name="assignee" value="{{ $ticket->assignee }}">
                <input type="hidden" name="due_date" value="{{ optional($ticket->due_date)->format('Y-m-d') }}">
                @foreach ($ticket->categories as $cat)
                    <input type="hidden" name="categories[]" value="{{ $cat->id }}">
                @endforeach
            </form>

            <dl class="text-sm space-y-2 mt-3">
                <div class="flex justify-between"><dt class="text-slate-400">Priority</dt><dd class="font-medium capitalize">{{ $ticket->priority }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Reporter</dt><dd>{{ $ticket->reporter ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Assignee</dt><dd>{{ $ticket->assignee ?: 'Unassigned' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Due date</dt><dd>{{ $ticket->due_date ? $ticket->due_date->format('M d, Y') : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">Created</dt><dd>{{ $ticket->created_at->format('M d, Y') }}</dd></div>
            </dl>
        </div>
    </div>
</div>
@endsection
