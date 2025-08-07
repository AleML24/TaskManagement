<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Keyword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_tasks_with_filters()
    {
        $keyword = Keyword::factory()->create();
        $task1 = Task::factory()->create(['title' => 'Test task', 'is_done' => false]);
        $task1->keywords()->attach($keyword);
        $task2 = Task::factory()->create(['title' => 'Another task', 'is_done' => true]);

        $response = $this->getJson('/api/tasks?title=Test&is_done=0&keyword_ids[]=' . $keyword->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Test task'])
            ->assertJsonMissing(['title' => 'Another task']);
    }

    #[Test]
    public function can_create_task_with_keywords()
    {
        $keyword = Keyword::factory()->create();

        $data = [
            'title' => 'New task',
            'is_done' => false,
            'keyword_ids' => [$keyword->id],
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New task'])
            ->assertJsonFragment(['id' => $keyword->id]);

        $this->assertDatabaseHas('tasks', ['title' => 'New task']);
        $this->assertDatabaseHas('keyword_task', [
            'task_id' => $response->json('data.id'),
            'keyword_id' => $keyword->id,
        ]);
    }

    #[Test]
    public function can_toggle_task_status()
    {
        $task = Task::factory()->create(['is_done' => false]);

        $response = $this->patchJson("/api/tasks/{$task->id}/toggleStatus");

        $response->assertStatus(200)
            ->assertJsonFragment(['is_done' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_done' => true,
        ]);
    }

    #[Test]
    public function can_update_task()
    {
        $task = Task::factory()->create(['title' => 'Old title', 'is_done' => false]);
        $keyword = Keyword::factory()->create();

        $data = [
            'title' => 'Updated title',
            'is_done' => true,
            'keyword_ids' => [$keyword->id],
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated title'])
            ->assertJsonFragment(['is_done' => true]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated title', 'is_done' => true]);
        $this->assertDatabaseHas('keyword_task', [
            'task_id' => $task->id,
            'keyword_id' => $keyword->id,
        ]);
    }

    #[Test]
    public function can_delete_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
