<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task()
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'status' => 'pending',
        ]);

        $response->assertStatus(201);
    }

    public function test_create_task_validation_fails()
    {
        $this->postJson('/api/tasks', [])
            ->assertStatus(422);
    }

    public function test_can_list_tasks()
    {
        Task::factory()->count(3)->create();

        $this->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create();

        $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'completed',
        ])->assertStatus(200);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(204);
    }
}
