@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h1 class="text-lg font-semibold mb-1">Categories</h1>
        <p class="text-sm text-slate-500 mb-4">Tag tickets (e.g. Hardware, Software, Network) so they're easy to filter on the board.</p>

        <form method="POST" action="{{ route('categories.store') }}" class="flex items-end gap-3 mb-6">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-medium text-slate-500 mb-1">Name</label>
                <input type="text" name="name" required placeholder="e.g. Printer" class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Color</label>
                <input type="color" name="color" value="#3B82F6" class="h-9 w-14 rounded-md border-slate-300">
            </div>
            <button class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium px-4 py-2 rounded-md text-sm">Add Category</button>
        </form>

        <div class="space-y-2">
            @foreach ($categories as $cat)
                <div class="flex items-center justify-between border border-slate-200 rounded-lg px-4 py-2.5">
                    <form method="POST" action="{{ route('categories.update', $cat) }}" class="flex items-center gap-3 flex-1">
                        @csrf @method('PUT')
                        <input type="color" name="color" value="{{ $cat->color }}" onchange="this.form.submit()" class="h-7 w-10 rounded border-slate-300">
                        <input type="text" name="name" value="{{ $cat->name }}" onblur="if(this.value !== '{{ $cat->name }}') this.form.submit()"
                               class="border-0 focus:ring-1 focus:ring-emerald-400 rounded text-sm font-medium bg-transparent flex-1">
                    </form>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span>{{ $cat->tickets_count }} ticket(s)</span>
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if ($categories->isEmpty())
                <p class="text-sm text-slate-400 text-center py-6">No categories yet — add your first one above.</p>
            @endif
        </div>
    </div>
</div>
@endsection
