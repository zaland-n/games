<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_web_roles(): void
    {
        $this->actingAs($this->userWithRole('admin'));

        $this->get(route('admin.roles.index'))->assertOk();
        $this->post(route('admin.roles.store'), ['name' => 'editor'])
            ->assertRedirect(route('admin.roles.index'));

        $role = Role::where('name', 'editor')->firstOrFail();
        $this->assertSame('web', $role->guard_name);

        $this->put(route('admin.roles.update', $role), ['name' => 'moderator'])
            ->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'moderator']);

        $this->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_non_admin_cannot_access_role_management(): void
    {
        $this->actingAs($this->userWithRole('klant'))
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    private function userWithRole(string $name): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => $name, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
