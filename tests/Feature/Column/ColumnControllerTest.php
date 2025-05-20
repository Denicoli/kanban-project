<?php

namespace Tests\Feature\Column;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ColumnControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_columns_for_owned_board()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $board = Board::factory()->create(['user_id' => $user->id]);
        Column::factory()->count(2)->create(['board_id' => $board->id]);

        $response = $this->getJson("/api/boards/{$board->id}/columns");

        $response->assertOk()
                 ->assertJsonCount(2);
    }

    public function test_user_cannot_list_columns_for_unowned_board()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $board = Board::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/boards/{$board->id}/columns");

        $response->assertStatus(403);
    }

    public function test_user_can_create_column_on_owned_board()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $board = Board::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/boards/{$board->id}/columns", [
            'title' => 'New Column',
            'order' => 1,
        ]);

        $response->assertCreated()
                 ->assertJsonFragment(['title' => 'New Column']);
    }
}