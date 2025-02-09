<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')->withPivot('role')->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name .' '. $this->last_name;
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function getTeamNamesAttribute()
    {
        return $this->teams->pluck('name')->join(', ');
    }

    public function projectsLed()
    {
        return $this->hasMany(Project::class, 'project_lead_id');
    }

    public function contributedProjects()
    {
        return $this->projects()->wherePivot('role', 'contributor');
    }

    public function watchedProjects()
    {
        return $this->projects()->wherePivot('role', 'watcher');
    }
}
