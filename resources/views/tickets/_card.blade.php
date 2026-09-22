@php
    $firstCategory = $ticket->categories->first();
    $priorityStyles = [
        'low' => 'background-color:#E2E8F0;color:#475569;',
        'medium' => 'background-color:#FEF3C7;color:#B45309;',
        'high' => 'background-color:#FFEDD5;color:#C2410C;',
        'urgent' => 'background-color:#FEE2E2;color:#B91C1C;',
    ];
@endphp

<div class="ticket-card group relative bg-white border border-slate-200 rounded-xl p-4 hover:shadow-sm cursor-grab active:cursor-grabbing"
     data-ticket-id="{{ $ticket->id }}">

    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
          onsubmit="return confirm('Delete this task?')"
          class="absolute top-2.5 right-2.5 opacity-0 group-hover:opacity-100 transition">
        @csrf @method('DELETE')
        <button class="w-5 h-5 flex items-center justify-center text-slate-300 hover:text-red-500 text-sm leading-none" title="Delete task">✕</button>
    </form>

    <a href="{{ route('tickets.show', $ticket) }}" class="block">
        <div class="flex items-center gap-2 mb-2 pr-5">
            @if ($firstCategory)
                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full" style="background-color: {{ $firstCategory->color }}1A; color: {{ $firstCategory->color }};">
                    {{ $firstCategory->name }}
                </span>
            @endif
            <span class="text-[11px] text-slate-400">{{ $ticket->created_at->format('m/d') }}</span>
        </div>

        <p class="text-sm font-semibold text-slate-800 leading-snug mb-1">{{ $ticket->title }}</p>

        @if ($ticket->description)
            <p class="text-xs text-slate-500 leading-snug line-clamp-2">{{ $ticket->description }}</p>
        @endif

        <div class="flex items-center justify-between mt-3">
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full" style="{{ $priorityStyles[$ticket->priority] ?? '' }}">
                {{ ucfirst($ticket->priority) }}
            </span>

            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-semibold text-white flex-shrink-0"
                  style="background-color: {{ ticketAvatarColor($ticket->assignee) }};"
                  title="{{ $ticket->assignee ?: 'Unassigned' }}">
                {{ ticketInitials($ticket->assignee) }}
            </span>
        </div>
    </a>
</div>
