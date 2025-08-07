<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskFactory extends Factory
{
    use HasFactory;
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(1),
            'is_done' => $this->faker->boolean(20),
        ];
    }
}
