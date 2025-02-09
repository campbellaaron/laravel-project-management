<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    // Define the mass assignable attributes
    protected $fillable = [
        'name', 'description', 'status', 'start_date', 'due_date', 'project_lead_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
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

    public function totalTrackedTime(): int
    {
        return $this->tasks->sum(function ($task) {
            return $task->timeEntries()
                ->whereNotNull('ended_at')
                ->get()
                ->sum(function ($entry) {
                    $started_at = Carbon::parse($entry->started_at);
                    $ended_at = Carbon::parse($entry->ended_at);
                    return max(0, $ended_at->diffInSeconds($started_at));
                });
        });
    }

}
