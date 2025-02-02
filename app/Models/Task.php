<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Automatically cast the due_date field to a Carbon instance
    protected $casts = ['due_date' => 'datetime'];

    protected $fillable = [
        'title', 'description', 'priority', 'assigned_to', 'due_date', 'project_id'
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
