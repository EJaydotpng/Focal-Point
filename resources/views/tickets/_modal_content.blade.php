@php
    $priorityClasses = [
        'low' => 'bg-slate-200 text-slate-600',
        'medium' => 'bg-amber-100 text-amber-700',
        'high' => 'bg-orange-100 text-orange-700',
        'urgent' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="p-6">
    <div class="flex items-start justify-between gap-4 pr-6">
        <div>
            <p class="text-xs text-slate-400 mb-1">Ticket #{{ $ticket->id }}</p>
            <h2 class="text-lg font-semibold text-slate-900">{{ $ticket->title }}</h2>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button type="button" onclick="openEditModal({{ $ticket->id }})" class="text-xs px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Edit</button>
            <button type="button" onclick="deleteTicketAjax({{ $ticket->id }})"
                    class="text-xs px-3 py-1.5 rounded-md border border-red-300 text-red-600 hover:bg-red-50">Delete</button>
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

    <div class="grid grid-cols-2 gap-4 mt-5 text-sm">
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Status</label>
            <select onchange="changeTicketStatus({{ $ticket->id }}, this.value)" class="w-full text-sm rounded-md border-slate-300">
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Priority</label>
            <p><span class="text-xs font-semibold px-2 py-1 rounded {{ $priorityClasses[$ticket->priority] ?? '' }}">{{ ucfirst($ticket->priority) }}</span></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Reporter</label>
            <p class="text-slate-700">{{ $ticket->reporter ?: '—' }}</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Assignee</label>
            <p class="text-slate-700">{{ $ticket->assignee ?: 'Unassigned' }}</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Due date</label>
            <p class="text-slate-700">{{ $ticket->due_date ? $ticket->due_date->format('M d, Y') : '—' }}</p>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1">Created</label>
            <p class="text-slate-700">{{ $ticket->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="mt-6 pt-5 border-t border-slate-100">
        <h3 class="font-medium text-sm mb-3">Attachments <span class="text-slate-400 font-normal">(problem reference & means of verification)</span></h3>

        <div class="flex items-center gap-2 mb-3">
            <input type="file" multiple accept="image/*,video/*" onchange="uploadAttachmentsAjax({{ $ticket->id }}, this)"
                   class="text-sm border border-slate-300 rounded-md p-1.5 flex-1">
        </div>

        @include('tickets._attachments', ['ticket' => $ticket])
    </div>
</div>
