<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'is_done'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_done' => 'boolean',
    ];

    protected $hidden = ['pivot'];

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_task', 'task_id', 'keyword_id');
    }
}
