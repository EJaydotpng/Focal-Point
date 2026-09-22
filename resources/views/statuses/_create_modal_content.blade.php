<div class="p-6">
    <h2 class="text-lg font-semibold text-slate-900 mb-1">New Column</h2>
    <p class="text-sm text-slate-500 mb-4">Add a stage to your board, e.g. "Waiting for Parts".</p>

    <form onsubmit="submitCreateStatus(event)" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required autofocus
                   class="w-full rounded-md border-slate-300 text-sm" placeholder="e.g. Waiting for Parts">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Color</label>
            <input type="color" name="color" value="#6B7280" class="h-9 w-14 rounded-md border-slate-300">
        </div>

        <p class="form-error text-sm text-red-600 hidden"></p>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">Create Column</button>
            <button type="button" onclick="Alpine.$data(document.body).closeModal()" class="text-sm text-slate-500 hover:text-slate-700">Cancel</button>
        </div>
    </form>
</div>
