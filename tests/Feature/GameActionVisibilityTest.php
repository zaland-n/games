<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GameActionVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_does_not_see_game_management_actions(): void
    {
        $role = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        Permission::create(['name' => 'product bekijken', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);
        $this->createGame();

        $this->get('/games')
            ->assertOk()
            ->assertDontSee('/games/create')
            ->assertDontSee('games/edit/')
            ->assertDontSee('games/destroy/');
    }

    public function test_admin_sees_game_management_actions(): void
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $permissions = collect([
            'product invoeren',
            'product aanpassen',
            'product verwijderen',
        ])->map(fn (string $name) => Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]));
        $role->givePermissionTo($permissions);

        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);
        $game = $this->createGame();

        $this->get('/games')
            ->assertOk()
            ->assertSee('/games/create')
            ->assertSee('/games/edit/'.$game->id)
            ->assertSee('/games/destroy/'.$game->id);
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
