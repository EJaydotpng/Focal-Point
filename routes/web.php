<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tickets');

// Kanban board + tickets
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
Route::post('/tickets/{ticket}/move', [TicketController::class, 'move'])->name('tickets.move');

// Attachments (images / videos as reference + MOV)
Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

// Progress indicators (statuses / columns), user-manageable
Route::get('/statuses', [StatusController::class, 'index'])->name('statuses.index');
Route::post('/statuses', [StatusController::class, 'store'])->name('statuses.store');
Route::put('/statuses/{status}', [StatusController::class, 'update'])->name('statuses.update');
Route::delete('/statuses/{status}', [StatusController::class, 'destroy'])->name('statuses.destroy');
Route::post('/statuses/reorder', [StatusController::class, 'reorder'])->name('statuses.reorder');

// Categories, user-manageable
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
