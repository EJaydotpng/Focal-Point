<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::orderBy('order')->withCount('tickets')->get();
        return view('statuses.index', compact('statuses'));
    }

    // Used by the board's "+ Column" button to load the create-column form into the modal.
    public function create(Request $request)
    {
        if ($this->wantsPartial($request)) {
            return view('statuses._create_modal_content');
        }

        return redirect()->route('statuses.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $maxOrder = Status::max('order') ?? 0;

        Status::create([
            'name' => $data['name'],
            'color' => $data['color'] ?? '#6B7280',
            'order' => $maxOrder + 1,
        ]);

        if ($this->wantsPartial($request)) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Progress indicator (column) created.');
    }

    public function update(Request $request, Status $status)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $status->update($data);

        return back()->with('success', 'Column updated.');
    }

    public function destroy(Status $status)
    {
        if ($status->tickets()->count() > 0) {
            return back()->with('error', 'Cannot delete a column that still has tickets in it. Move or delete those tickets first.');
        }

        $status->delete();

        return back()->with('success', 'Column deleted.');
    }

    // Drag-and-drop reordering of the columns themselves
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:statuses,id',
        ]);

        foreach ($data['order'] as $index => $statusId) {
            Status::where('id', $statusId)->update(['order' => $index]);
        }

        return response()->json(['ok' => true]);
    }

    // True when the request came from our own fetch() calls (board modal),
    // as opposed to a normal browser page load / direct link.
    protected function wantsPartial(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
