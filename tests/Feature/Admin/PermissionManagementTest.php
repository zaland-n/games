<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_web_permissions(): void
    {
        $this->actingAs($this->userWithRole('admin'));

        $this->get(route('admin.permissions.index'))->assertOk();
        $this->post(route('admin.permissions.store'), ['name' => 'catalogus bekijken'])
            ->assertRedirect(route('admin.permissions.index'));

        $permission = Permission::where('name', 'catalogus bekijken')->firstOrFail();
        $this->assertSame('web', $permission->guard_name);

        $this->put(route('admin.permissions.update', $permission), ['name' => 'catalogus lezen'])
            ->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'catalogus lezen']);

        $this->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_non_admin_cannot_access_permission_management(): void
    {
        $this->actingAs($this->userWithRole('klant'))
            ->get(route('admin.permissions.index'))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_login_from_permission_management(): void
    {
        $this->get(route('admin.permissions.index'))
            ->assertRedirect(route('login'));
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
