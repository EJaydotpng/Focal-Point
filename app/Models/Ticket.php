<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status_id', 'priority',
        'reporter', 'assignee', 'due_date', 'board_order',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class)->latest();
    }

    public function images()
    {
        return $this->attachments()->where('type', 'image');
    }

    public function videos()
    {
        return $this->attachments()->where('type', 'video');
    }
}
