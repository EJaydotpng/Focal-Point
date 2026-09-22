<div class="p-8 text-center">
    <p class="text-slate-600 text-sm mb-4">You need at least one column (e.g. "To Do") before you can create a ticket.</p>
    <button type="button" onclick="openModal('{{ route('statuses.create') }}')"
            class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">
        + Create your first column
    </button>
</div>
