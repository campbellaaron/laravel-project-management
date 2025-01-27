<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // Define the mass assignable attributes
    protected $fillable = [
        'name', 'description', 'status'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
