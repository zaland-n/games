<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GameAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_game_routes(): void
    {
        $this->get('/games')->assertRedirect(route('login'));
        $this->get('/games/show/1')->assertRedirect(route('login'));
        $this->get('/games/create')->assertRedirect(route('login'));
        $this->post('/games/store')->assertRedirect(route('login'));
        $this->get('/games/edit/1')->assertRedirect(route('login'));
        $this->get('/games/update/1')->assertRedirect(route('login'));
        $this->post('/games/update/1')->assertRedirect(route('login'));
        $this->post('/games/destroy/1')->assertRedirect(route('login'));
    }

    public function test_customer_can_view_only_the_games_overview(): void
    {
        $user = $this->userWithRole('klant');
        $this->actingAs($user);

        $this->get('/games')->assertOk();
        $this->get('/games/show/1')->assertForbidden();
        $this->get('/games/create')->assertForbidden();
        $this->post('/games/store')->assertForbidden();
        $this->get('/games/edit/1')->assertForbidden();
        $this->get('/games/update/1')->assertForbidden();
        $this->post('/games/update/1')->assertForbidden();
        $this->post('/games/destroy/1')->assertForbidden();
    }

    public function test_admin_can_access_and_manage_games(): void
    {
        $this->actingAs($this->userWithRole('admin'));
        $game = $this->createGame();

        $this->get('/games')->assertOk();
        $this->get('/games/show/'.$game->id)->assertOk();
        $this->get('/games/create')->assertOk();
        $this->get('/games/edit/'.$game->id)->assertOk();
        $this->get('/games/update/'.$game->id)->assertOk();
        $this->post('/games/store', [
            'game_name' => 'New game',
            'platform' => 'PC',
            'genre' => 'Adventure',
            'rating' => 8,
        ])->assertRedirect('/games');
        $this->post('/games/update/'.$game->id, [
            'game_name' => 'Updated game',
            'platform' => 'PC',
            'genre' => 'Adventure',
            'rating' => 9,
        ])->assertRedirect('/games');
        $this->post('/games/destroy/'.$game->id)->assertRedirect('/games');
        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createGame(): Game
    {
        return Game::create([
            'game_name' => 'Test game',
            'platform' => 'PC',
            'genre' => 'Adventure',
            'rating' => 8,
        ]);
    }
}
