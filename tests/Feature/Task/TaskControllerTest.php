<?php

namespace Tests\Feature\Task;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_tasks_for_owned_column()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $board = Board::factory()->create(['user_id' => $user->id]);
        $column = Column::factory()->create(['board_id' => $board->id]);
        Task::factory()->count(3)->create(['column_id' => $column->id]);

        $response = $this->getJson("/api/columns/{$column->id}/tasks");

        $response->assertOk()
                 ->assertJsonCount(3);
    }

    public function test_user_cannot_list_tasks_for_unowned_column()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $board = Board::factory()->create(['user_id' => $otherUser->id]);
        $column = Column::factory()->create(['board_id' => $board->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/columns/{$column->id}/tasks");

        $response->assertStatus(403);
    }

    public function test_user_can_create_task_on_owned_column()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $board = Board::factory()->create(['user_id' => $user->id]);
        $column = Column::factory()->create(['board_id' => $board->id]);

        $response = $this->postJson("/api/columns/{$column->id}/tasks", [
            'title' => 'New Task',
            'description' => 'Task description',
            'order' => 1,
        ]);

        $response->assertCreated()
                 ->assertJsonFragment(['title' => 'New Task']);
    }
}