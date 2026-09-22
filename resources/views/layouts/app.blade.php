<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Ticketing') | IT Support Desk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col"
      x-data="{
          modalOpen: false,
          modalLoading: false,
          modalHtml: '',
          needsReload: false,
          async openTicketModal(url) {
              this.modalOpen = true;
              this.modalLoading = true;
              this.modalHtml = '';
              this.needsReload = false;
              try {
                  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                  this.modalHtml = await res.text();
              } catch (e) {
                  this.modalHtml = '<p class=&quot;p-8 text-center text-red-600 text-sm&quot;>Could not load this ticket. Please try again.</p>';
              }
              this.modalLoading = false;
          },
          closeModal() {
              this.modalOpen = false;
              this.modalHtml = '';
              if (this.needsReload) { window.location.reload(); }
          }
      }">

    <nav class="bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
            <a href="{{ route('tickets.index') }}" class="font-semibold text-lg flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                IT Support Desk
            </a>
            <div class="flex items-center gap-1 text-sm">
                <a href="{{ route('tickets.index') }}" class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('tickets.*') ? 'bg-slate-700' : '' }}">Board</a>
                <a href="{{ route('statuses.index') }}" class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('statuses.*') ? 'bg-slate-700' : '' }}">Columns</a>
                <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded hover:bg-slate-700 {{ request()->routeIs('categories.*') ? 'bg-slate-700' : '' }}">Categories</a>
                <span class="w-px h-5 bg-slate-700 mx-1"></span>
                <button type="button" onclick="openModal('{{ route('statuses.create') }}')" class="px-3 py-2 rounded hover:bg-slate-700">+ Column</button>
                <button type="button" onclick="openModal('{{ route('categories.create') }}')" class="px-3 py-2 rounded hover:bg-slate-700">+ Category</button>
                <button type="button" onclick="openModal('{{ route('tickets.create') }}')" class="ml-2 px-3 py-2 rounded bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-medium">+ New Ticket</button>
            </div>
        </div>
    </nav>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-slate-400 py-4">Internal IT Ticketing System</footer>

    {{-- Ticket modal --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(15, 23, 42, 0.55);"
         @click.self="closeModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[88vh] overflow-y-auto relative" @keydown.escape.window="closeModal()">
            <button @click="closeModal()" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 text-lg z-10">✕</button>

            <div x-show="modalLoading" class="p-16 text-center text-slate-400 text-sm">Loading ticket…</div>
            <div x-show="!modalLoading" x-html="modalHtml"></div>
        </div>
    </div>

    <script>
        // Plain (non-Alpine) helper functions used by ticket modal content that gets
        // injected via innerHTML - <script> tags inside that content would never run,
        // so these live here once and are called via plain onclick="..." attributes.
        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        }

        // Bridges plain onclick="..." handlers (which run in global scope) to the
        // openTicketModal method that actually lives inside Alpine's x-data on <body>.
        function openTicketModal(url) {
            Alpine.$data(document.body).openTicketModal(url);
        }

        // Generic alias - used when opening non-ticket modals (new column, new category)
        // so the calling code reads naturally, but it's the exact same modal underneath.
        function openModal(url) {
            openTicketModal(url);
        }

        async function changeTicketStatus(ticketId, statusId) {
            try {
                await fetch(`/tickets/${ticketId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ status_id: statusId }),
                });
                Alpine.$data(document.body).needsReload = true;
            } catch (e) {
                alert('Could not update status. Please try again.');
            }
        }

        async function deleteTicketAjax(ticketId) {
            if (!confirm('Delete this task? This also removes its attachments.')) return;
            try {
                await fetch(`/tickets/${ticketId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const root = Alpine.$data(document.body);
                root.needsReload = true;
                root.closeModal();
            } catch (e) {
                alert('Could not delete this task. Please try again.');
            }
        }

        async function deleteAttachmentAjax(attachmentId) {
            if (!confirm('Remove this file?')) return;
            try {
                const res = await fetch(`/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json();
                document.getElementById('attachments-grid').outerHTML = data.html;
                Alpine.$data(document.body).needsReload = true;
            } catch (e) {
                alert('Could not remove that file. Please try again.');
            }
        }

        async function uploadAttachmentsAjax(ticketId, inputEl) {
            if (!inputEl.files.length) return;
            const formData = new FormData();
            for (const file of inputEl.files) formData.append('attachments[]', file);
            try {
                const res = await fetch(`/tickets/${ticketId}/attachments`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                const data = await res.json();
                document.getElementById('attachments-grid').outerHTML = data.html;
                inputEl.value = '';
                Alpine.$data(document.body).needsReload = true;
            } catch (e) {
                alert('Upload failed. Please check the file size/type and try again.');
            }
        }

        // Swaps the modal from "view" mode into the edit form (loaded via AJAX).
        function openEditModal(ticketId) {
            openTicketModal(`/tickets/${ticketId}/edit`);
        }

        // Submits the edit form inside the modal without leaving/reloading the page.
        async function submitTicketEdit(event, ticketId) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const errorEl = form.querySelector('.edit-error');
            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving…';

            const formData = new FormData(form);
            formData.append('_method', 'PUT'); // Laravel method spoofing (works with multipart uploads)

            try {
                const res = await fetch(`/tickets/${ticketId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => null);
                    const message = data?.message || 'Please check the form and try again.';
                    errorEl.textContent = message;
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Save Changes';
                    return;
                }

                Alpine.$data(document.body).needsReload = true;
                // Jump back to the (now updated) view mode inside the same modal.
                openTicketModal(`/tickets/${ticketId}`);
            } catch (e) {
                errorEl.textContent = 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            }
        }
        // Submits the "create ticket" modal form without leaving/reloading the page.
        async function submitCreateTicket(event) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const errorEl = form.querySelector('.form-error');
            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating…';

            const formData = new FormData(form);

            try {
                const res = await fetch('{{ route('tickets.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => null);
                    errorEl.textContent = data?.message || 'Please check the form and try again.';
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Ticket';
                    return;
                }

                const root = Alpine.$data(document.body);
                root.needsReload = true;
                root.closeModal();
            } catch (e) {
                errorEl.textContent = 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Ticket';
            }
        }

        // Submits the "create column" modal form without leaving/reloading the page.
        async function submitCreateStatus(event) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const errorEl = form.querySelector('.form-error');
            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating…';

            const formData = new FormData(form);

            try {
                const res = await fetch('{{ route('statuses.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => null);
                    errorEl.textContent = data?.message || 'Please check the form and try again.';
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Column';
                    return;
                }

                const root = Alpine.$data(document.body);
                root.needsReload = true;
                root.closeModal();
            } catch (e) {
                errorEl.textContent = 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Column';
            }
        }

        // Submits the "create category" modal form without leaving/reloading the page.
        async function submitCreateCategory(event) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const errorEl = form.querySelector('.form-error');
            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating…';

            const formData = new FormData(form);

            try {
                const res = await fetch('{{ route('categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => null);
                    errorEl.textContent = data?.message || 'Please check the form and try again. (Category names must be unique.)';
                    errorEl.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create Category';
                    return;
                }

                const root = Alpine.$data(document.body);
                root.needsReload = true;
                root.closeModal();
            } catch (e) {
                errorEl.textContent = 'Something went wrong. Please try again.';
                errorEl.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Category';
            }
        }
    </script>
</body>
</html>
