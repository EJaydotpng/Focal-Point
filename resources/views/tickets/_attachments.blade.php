<div id="attachments-grid">
    @if ($ticket->attachments->isEmpty())
        <p class="text-sm text-slate-400">No files uploaded yet.</p>
    @else
        <div class="grid grid-cols-3 gap-3">
            @foreach ($ticket->attachments as $attachment)
                <div class="relative group border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                    @if ($attachment->type === 'image')
                        <a href="{{ $attachment->url }}" target="_blank">
                            <img src="{{ $attachment->url }}" class="w-full h-24 object-cover">
                        </a>
                    @elseif ($attachment->type === 'video')
                        <video src="{{ $attachment->url }}" class="w-full h-24 object-cover" controls></video>
                    @else
                        <a href="{{ $attachment->url }}" target="_blank" class="flex items-center justify-center h-24 text-slate-400 text-xs px-2 text-center">📄 {{ $attachment->original_name }}</a>
                    @endif
                    <div class="p-1.5 text-[10px] text-slate-500 truncate">{{ $attachment->original_name }}</div>
                    <button type="button"
                            onclick="deleteAttachmentAjax({{ $attachment->id }})"
                            class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition bg-black/60 text-white text-xs w-5 h-5 rounded-full leading-none">✕</button>
                </div>
            @endforeach
        </div>
    @endif
</div>
