<div class="p-6">
    <div class="flex items-start justify-between gap-4 pr-6 mb-4">
        <div>
            <p class="text-xs text-slate-400 mb-1">Ticket #{{ $ticket->id }}</p>
            <h2 class="text-lg font-semibold text-slate-900">Edit Task</h2>
        </div>
        <button type="button" onclick="openTicketModal('{{ route('tickets.show', $ticket) }}')" class="text-xs px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">
            ← Back to task
        </button>
    </div>

    <form onsubmit="submitTicketEdit(event, {{ $ticket->id }})" enctype="multipart/form-data" class="space-y-4">

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ $ticket->title }}" required class="w-full rounded-md border-slate-300 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-md border-slate-300 text-sm">{{ $ticket->description }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Column / Status</label>
                <select name="status_id" required class="w-full rounded-md border-slate-300 text-sm">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Priority</label>
                <select name="priority" class="w-full rounded-md border-slate-300 text-sm">
                    @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                        <option value="{{ $p }}" {{ $ticket->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Reporter</label>
                <input type="text" name="reporter" value="{{ $ticket->reporter }}" class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Assignee</label>
                <input type="text" name="assignee" value="{{ $ticket->assignee }}" class="w-full rounded-md border-slate-300 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Due date</label>
            <input type="date" name="due_date" value="{{ optional($ticket->due_date)->format('Y-m-d') }}" class="rounded-md border-slate-300 text-sm">
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

        <p class="edit-error text-sm text-red-600 hidden"></p>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">Save Changes</button>
            <button type="button" onclick="openTicketModal('{{ route('tickets.show', $ticket) }}')" class="text-sm text-slate-500 hover:text-slate-700">Cancel</button>
        </div>
    </form>
</div>
