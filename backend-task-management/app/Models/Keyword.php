<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = ['name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = ['pivot'];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'keyword_task', 'keyword_id', 'task_id');
    }
}
