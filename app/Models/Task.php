<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Project;
use Carbon\Carbon;

class Task extends Model
{
    // Automatically cast the due_date field to a Carbon instance
    protected $casts = ['due_date' => 'datetime'];

    protected $fillable = [
        'title', 'description', 'priority', 'assigned_to', 'due_date', 'project_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->task_key = self::generateTaskKey($task->project_id);
        });
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function generateTaskKey($projectId)
    {
        // Fetch the project
        $project = Project::find($projectId);

        // Generate the prefix from the project name (e.g., "Web Development" → "WEB")
        $prefix = strtoupper(Str::slug($project->name, ''));
        $prefix = substr($prefix, 0, 3); // First 3 letters (e.g., WEB)

        // Count existing tasks for this project and increment
        $taskCount = Task::where('project_id', $projectId)->count() + 1;

        return "{$prefix}-" . str_pad($taskCount, 4, '0', STR_PAD_LEFT);
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function totalTrackedTime()
    {
        return $this->timeEntries()
            ->whereNotNull('ended_at') // Ensure only completed time entries are counted
            ->get()
            ->sum(function ($entry) {
                return Carbon::parse($entry->started_at)->diffInSeconds(Carbon::parse($entry->ended_at));
            });
    }
}
