<?php

namespace Tests\Feature\Board;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_boards()
    {
        $response = $this->getJson('/api/boards');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_their_boards()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Board::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/boards');
        $response->assertOk()
                 ->assertJsonCount(3);
    }

    public function test_user_cannot_access_other_boards()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $board = Board::factory()->count(3)->create(['user_id' => $otherUser->id])->first();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/boards/{$board->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_create_board()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/boards', [
            'name' => 'New Board',
            'description' => 'Board description',
        ]);

        $response->assertCreated()
                 ->assertJsonFragment([
                     'name' => 'New Board'
                 ]);

        $this->assertDatabaseHas('boards', [
            'name' => 'New Board',
            'description' => 'Board description',
            'user_id' => $user->id,
        ]);
    }
}