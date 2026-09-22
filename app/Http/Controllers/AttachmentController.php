<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    // Used by the ticket modal's upload form (AJAX) so a new file appears
    // without leaving/reloading the board.
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,mkv|max:102400',
        ]);

        if ($request->hasFile('attachments')) {
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

        $ticket->load('attachments');

        if ($this->wantsPartial($request)) {
            return response()->json([
                'ok' => true,
                'html' => view('tickets._attachments', compact('ticket'))->render(),
            ]);
        }

        return back()->with('success', 'Files uploaded.');
    }

    public function destroy(Request $request, Attachment $attachment)
    {
        $ticket = $attachment->ticket;
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        if ($this->wantsPartial($request)) {
            $ticket->load('attachments');
            return response()->json([
                'ok' => true,
                'html' => view('tickets._attachments', compact('ticket'))->render(),
            ]);
        }

        return back()->with('success', 'Attachment removed.');
    }

    protected function wantsPartial(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
