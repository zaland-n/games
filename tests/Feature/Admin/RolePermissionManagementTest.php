<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_role_permission_assignments(): void
    {
        $this->actingAs($this->admin());
        $role = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        $otherRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'product bekijken', 'guard_name' => 'web']);
        $otherPermission = Permission::create(['name' => 'product bestellen', 'guard_name' => 'web']);

        $this->get(route('admin.role-permissions.index'))->assertOk();
        $this->post(route('admin.role-permissions.store'), [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ])->assertRedirect(route('admin.role-permissions.index'));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission));

        $key = $role->id.'-'.$permission->id;
        $this->get(route('admin.role-permissions.edit', $key))->assertOk();
        $this->put(route('admin.role-permissions.update', $key), [
            'role_id' => $otherRole->id,
            'permission_id' => $otherPermission->id,
        ])->assertRedirect(route('admin.role-permissions.index'));
        $this->assertFalse($role->fresh()->hasPermissionTo($permission));
        $this->assertTrue($otherRole->fresh()->hasPermissionTo($otherPermission));

        $this->delete(route('admin.role-permissions.destroy', $otherRole->id.'-'.$otherPermission->id))
            ->assertRedirect(route('admin.role-permissions.index'));
        $this->assertFalse($otherRole->fresh()->hasPermissionTo($otherPermission));
    }

    public function test_duplicate_role_permission_assignment_is_rejected(): void
    {
        $this->actingAs($this->admin());
        $role = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'product bekijken', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->from(route('admin.role-permissions.create'))
            ->post(route('admin.role-permissions.store'), [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ])
            ->assertSessionHasErrors('permission_id');
    }

    public function test_non_admin_cannot_access_role_permission_assignments(): void
    {
        $customerRole = Role::create(['name' => 'klant', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($customerRole);

        $this->actingAs($user)->get(route('admin.role-permissions.index'))->assertForbidden();
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
