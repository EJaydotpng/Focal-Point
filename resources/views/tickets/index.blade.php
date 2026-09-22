@extends('layouts.app')

@section('title', 'Board')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Ticket Board</h1>
    <form method="GET" action="{{ route('tickets.index') }}" class="flex items-center gap-2">
        <label class="text-sm text-slate-500">Filter by category:</label>
        <select name="category" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-md">
            <option value="">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        @if (request('category'))
            <a href="{{ route('tickets.index') }}" class="text-sm text-slate-400 hover:text-slate-600">Clear</a>
        @endif
    </form>
</div>

@if ($statuses->isEmpty())
    <div class="bg-white rounded-xl border border-dashed border-slate-300 p-10 text-center text-slate-500">
        No columns yet. <button type="button" onclick="openModal('{{ route('statuses.create') }}')" class="text-emerald-600 underline">Create your first progress indicator</button> (e.g. To Do, In Progress, Checking, Done).
    </div>
@else
<div class="flex gap-4 overflow-x-auto pb-4">
    @foreach ($statuses as $status)
        <div class="flex-shrink-0 w-72 bg-white rounded-xl border border-slate-200 flex flex-col max-h-[75vh]">
            <div class="px-3 py-2 border-b border-slate-200 flex items-center justify-between rounded-t-xl" style="background-color: {{ $status->color }}1A;">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $status->color }};"></span>
                    <span class="font-medium text-sm">{{ $status->name }}</span>
                    <span class="text-xs text-slate-400">({{ $status->tickets->count() }})</span>
                </div>
            </div>

            <div class="p-2 space-y-2 overflow-y-auto flex-1 board-column" data-status-id="{{ $status->id }}">
                @foreach ($status->tickets as $ticket)
                    <a href="{{ route('tickets.show', $ticket) }}"
                       @click.prevent="openTicketModal('{{ route('tickets.show', $ticket) }}')"
                       data-ticket-id="{{ $ticket->id }}"
                       class="ticket-card block bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg p-3 cursor-grab active:cursor-grabbing">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-slate-800 leading-snug">{{ $ticket->title }}</p>
                            <span class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded
                                @class([
                                    'bg-slate-200 text-slate-600' => $ticket->priority === 'low',
                                    'bg-amber-100 text-amber-700' => $ticket->priority === 'medium',
                                    'bg-orange-100 text-orange-700' => $ticket->priority === 'high',
                                    'bg-red-100 text-red-700' => $ticket->priority === 'urgent',
                                ])">{{ $ticket->priority }}</span>
                        </div>

                        @if ($ticket->categories->count())
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach ($ticket->categories as $cat)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full" style="background-color: {{ $cat->color }}22; color: {{ $cat->color }};">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-2 text-[11px] text-slate-400">
                            <span>{{ $ticket->assignee ?: 'Unassigned' }}</span>
                            <span class="flex items-center gap-2">
                                @php($attCount = $ticket->attachments()->count())
                                @if($attCount)
                                    <span title="Attachments">📎 {{ $attCount }}</span>
                                @endif
                                @if ($ticket->due_date)
                                    {{ $ticket->due_date->format('M d') }}
                                @endif
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = '{{ csrf_token() }}';
    document.querySelectorAll('.board-column').forEach(function (column) {
        new Sortable(column, {
            group: 'tickets',
            animation: 150,
            ghostClass: 'opacity-40',
            onEnd: function (evt) {
                const destColumn = evt.to;
                const statusId = destColumn.dataset.statusId;
                const ticketId = evt.item.dataset.ticketId;
                const order = Array.from(destColumn.querySelectorAll('.ticket-card')).map(el => el.dataset.ticketId);

                fetch(`/tickets/${ticketId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status_id: statusId, order: order }),
                }).catch(() => alert('Could not save the move. Please refresh and try again.'));
            }
        });
    });
});
</script>
@endsection
