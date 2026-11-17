<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeheimPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/geheim')
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_the_secret_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/geheim')
            ->assertOk()
            ->assertSee('<h1>Geheime pagina</h1>', false);
    }
}
