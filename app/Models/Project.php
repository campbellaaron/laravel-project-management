<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // Define the mass assignable attributes
    protected $fillable = [
        'name', 'description', 'status', 'start_date', 'due_date',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')->withPivot('role')->withTimestamps();
    }

    public function contributors()
    {
        return $this->users()->wherePivot('role', 'contributor');
    }

    public function watchers()
    {
        return $this->users()->wherePivot('role', 'watcher');
    }

    public function getFormattedStatusAttribute()
    {
        return match ($this->status) {
            'open' => 'Open',
            'in-progress' => 'In Progress',
            'completed' => 'Completed',
            default => ucfirst($this->status), // Fallback
        };
    }

    public function projectLead()
    {
        return $this->belongsTo(User::class, 'project_lead_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
