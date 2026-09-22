<div class="p-6">
    <h2 class="text-lg font-semibold text-slate-900 mb-4">Create Ticket</h2>

    <form onsubmit="submitCreateTicket(event)" enctype="multipart/form-data" class="space-y-4">

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" required autofocus
                   class="w-full rounded-md border-slate-300 text-sm" placeholder="e.g. Printer on 3rd floor not printing">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-md border-slate-300 text-sm"
                      placeholder="Describe the issue in detail..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Column / Status</label>
                <select name="status_id" required class="w-full rounded-md border-slate-300 text-sm">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Priority</label>
                <select name="priority" class="w-full rounded-md border-slate-300 text-sm">
                    @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                        <option value="{{ $p }}" {{ $p === 'medium' ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Reporter</label>
                <input type="text" name="reporter" class="w-full rounded-md border-slate-300 text-sm" placeholder="Who is reporting this?">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Assignee</label>
                <input type="text" name="assignee" class="w-full rounded-md border-slate-300 text-sm" placeholder="Who will handle this?">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Due date</label>
            <input type="date" name="due_date" class="rounded-md border-slate-300 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Categories</label>
            @if ($categories->isEmpty())
                <p class="text-sm text-slate-400">
                    No categories yet.
                    <button type="button" onclick="openModal('{{ route('categories.create') }}')" class="text-emerald-600 underline">Create one</button>
                </p>
            @else
                <div class="flex flex-wrap gap-3">
                    @foreach ($categories as $cat)
                        <label class="inline-flex items-center gap-1.5 text-sm bg-slate-50 border border-slate-200 rounded-full px-3 py-1 cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}">
                            <span style="color: {{ $cat->color }};">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Attachments (images / videos)</label>
            <input type="file" name="attachments[]" multiple accept="image/*,video/*"
                   class="w-full text-sm border border-slate-300 rounded-md p-2">
            <p class="text-xs text-slate-400 mt-1">Use these to show the problem, and later as your means of verification (MOV) once it's resolved. Max 100MB per file.</p>
        </div>

        <p class="form-error text-sm text-red-600 hidden"></p>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">Create Ticket</button>
            <button type="button" onclick="Alpine.$data(document.body).closeModal()" class="text-sm text-slate-500 hover:text-slate-700">Cancel</button>
        </div>
    </form>
</div>
