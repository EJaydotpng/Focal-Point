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

    public function create(Request $request)
    {
        $statuses = Status::orderBy('order')->get();
        $categories = Category::orderBy('name')->get();

        if ($statuses->isEmpty()) {
            if ($this->wantsPartial($request)) {
                return view('tickets._no_columns_modal_content');
            }
            return redirect()->route('statuses.index')
                ->with('error', 'Create at least one progress indicator (e.g. To Do) before adding tickets.');
        }

        if ($this->wantsPartial($request)) {
            return view('tickets._create_modal_content', compact('statuses', 'categories'));
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

        if ($this->wantsPartial($request)) {
            return response()->json(['ok' => true, 'ticket_id' => $ticket->id]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created.');
    }

    // Full ticket page (used for direct links/bookmarks, and as a fallback without JS).
    public function show(Ticket $ticket)
    {
        $ticket->load(['status', 'categories', 'attachments']);
        $statuses = Status::orderBy('order')->get();
        $categories = Category::orderBy('name')->get();

        // If this was opened via the board's modal (fetch/XHR), return just the
        // inner content, no layout wrapper - so it can drop straight into the modal.
        if ($this->wantsPartial(request())) {
            return view('tickets._modal_content', compact('ticket', 'statuses', 'categories'));
        }

        return view('tickets.show', compact('ticket', 'statuses', 'categories'));
    }

    public function edit(Request $request, Ticket $ticket)
    {
        $ticket->load('categories');
        $statuses = Status::orderBy('order')->get();
        $categories = Category::orderBy('name')->get();

        if ($this->wantsPartial($request)) {
            return view('tickets._edit_modal_content', compact('ticket', 'statuses', 'categories'));
        }

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

        if ($this->wantsPartial($request)) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        foreach ($ticket->attachments as $attachment) {
            \Storage::disk('public')->delete($attachment->file_path);
        }
        $ticket->delete();

        if ($this->wantsPartial($request)) {
            return response()->json(['ok' => true]);
        }

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

    // AJAX endpoint used by the status <select> inside the ticket modal, so changing
    // status doesn't need to navigate away or resubmit the whole ticket form.
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $maxOrder = Ticket::where('status_id', $data['status_id'])->max('board_order') ?? 0;

        $ticket->update([
            'status_id' => $data['status_id'],
            'board_order' => $maxOrder + 1,
        ]);

        return response()->json(['ok' => true, 'status_name' => $ticket->status->name]);
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

    // True when the request came from our own fetch() calls (board modal, AJAX actions),
    // as opposed to a normal browser page load / direct link.
    protected function wantsPartial(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
