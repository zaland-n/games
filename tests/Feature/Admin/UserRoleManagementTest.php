<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_user_role_assignments(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $role = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        $otherRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $this->get(route('admin.user-roles.index'))->assertOk();
        $this->post(route('admin.user-roles.store'), [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ])->assertRedirect(route('admin.user-roles.index'));
        $this->assertTrue($user->fresh()->hasRole($role));

        $key = $user->id.'-'.$role->id;
        $this->get(route('admin.user-roles.edit', $key))->assertOk();
        $this->put(route('admin.user-roles.update', $key), [
            'user_id' => $otherUser->id,
            'role_id' => $otherRole->id,
        ])->assertRedirect(route('admin.user-roles.index'));
        $this->assertFalse($user->fresh()->hasRole($role));
        $this->assertTrue($otherUser->fresh()->hasRole($otherRole));

        $this->delete(route('admin.user-roles.destroy', $otherUser->id.'-'.$otherRole->id))
            ->assertRedirect(route('admin.user-roles.index'));
        $this->assertFalse($otherUser->fresh()->hasRole($otherRole));
    }

    public function test_duplicate_user_role_assignment_is_rejected(): void
    {
        $this->actingAs($this->admin());
        $user = User::factory()->create();
        $role = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->from(route('admin.user-roles.create'))
            ->post(route('admin.user-roles.store'), [
                'user_id' => $user->id,
                'role_id' => $role->id,
            ])
            ->assertSessionHasErrors('role_id');
    }

    public function test_non_admin_cannot_access_user_role_assignments(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)->get(route('admin.user-roles.index'))->assertForbidden();
    }

    private function admin(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
