<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Status;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // Kanban board - the main screen
    public function index(Request $request)
    {
        $statuses = Status::orderBy('order')
            ->with(['tickets' => function ($q) use ($request) {
                if ($request->filled('category')) {
                    $q->whereHas('categories', fn ($c) => $c->where('categories.id', $request->category));
                }
                $q->with('categories')->orderBy('board_order');
            }])
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('tickets.index', compact('statuses', 'categories'));
    }

    public function create()
    {
        $statuses = Status::orderBy('order')->get();
        $categories = Category::orderBy('name')->get();

        if ($statuses->isEmpty()) {
            return redirect()->route('statuses.index')
                ->with('error', 'Create at least one progress indicator (e.g. To Do) before adding tickets.');
        }

        return view('tickets.create', compact('statuses', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'reporter' => 'nullable|string|max:100',
            'assignee' => 'nullable|string|max:100',
            'due_date' => 'nullable|date',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv|max:102400',
        ]);

        $maxOrder = Ticket::where('status_id', $data['status_id'])->max('board_order') ?? 0;
        $data['board_order'] = $maxOrder + 1;

        $ticket = Ticket::create($data);

        if (!empty($data['categories'])) {
            $ticket->categories()->sync($data['categories']);
        }

        $this->storeAttachments($request, $ticket);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created.');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['status', 'categories', 'attachments']);
        $statuses = Status::orderBy('order')->get();
        $categories = Category::orderBy('name')->get();

        return view('tickets.show', compact('ticket', 'statuses', 'categories'));
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load('categories');
        $statuses = Status::orderBy('order')->get();
        $categories = Category::orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'statuses', 'categories'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'reporter' => 'nullable|string|max:100',
            'assignee' => 'nullable|string|max:100',
            'due_date' => 'nullable|date',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv|max:102400',
        ]);

        $ticket->update($data);
        $ticket->categories()->sync($data['categories'] ?? []);

        $this->storeAttachments($request, $ticket);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function destroy(Ticket $ticket)
    {
        foreach ($ticket->attachments as $attachment) {
            \Storage::disk('public')->delete($attachment->file_path);
        }
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted.');
    }

    // AJAX endpoint used by the drag-and-drop board (SortableJS) to move a ticket
    // to a new column and/or a new position within that column.
    public function move(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'order' => 'required|array',      // ordered list of ticket ids in the destination column
            'order.*' => 'integer|exists:tickets,id',
        ]);

        $ticket->update(['status_id' => $data['status_id']]);

        foreach ($data['order'] as $index => $ticketId) {
            Ticket::where('id', $ticketId)->update([
                'board_order' => $index,
                'status_id' => $data['status_id'],
            ]);
        }

        return response()->json(['ok' => true]);
    }

    protected function storeAttachments(Request $request, Ticket $ticket): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $mime = $file->getMimeType();
            $type = str_starts_with($mime, 'image/') ? 'image'
                : (str_starts_with($mime, 'video/') ? 'video' : 'other');

            $path = $file->store('tickets/' . $ticket->id, 'public');

            $ticket->attachments()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mime,
                'type' => $type,
                'size' => $file->getSize(),
            ]);
        }
    }
}
