<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_the_games_overview(): void
    {
        $this->get('/games')->assertOk();
    }

    public function test_guests_are_redirected_to_login_from_game_management_routes(): void
    {
        $this->get('/games/create')->assertRedirect(route('login'));
        $this->post('/games/store')->assertRedirect(route('login'));
        $this->get('/games/edit/1')->assertRedirect(route('login'));
        $this->get('/games/update/1')->assertRedirect(route('login'));
        $this->post('/games/update/1')->assertRedirect(route('login'));
        $this->post('/games/destroy/1')->assertRedirect(route('login'));
    }
}
