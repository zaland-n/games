<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $assignments = DB::table('role_has_permissions')
            ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('roles.guard_name', 'web')
            ->where('permissions.guard_name', 'web')
            ->select('role_has_permissions.role_id', 'role_has_permissions.permission_id', 'roles.name as role_name', 'permissions.name as permission_name')
            ->orderBy('roles.name')
            ->orderBy('permissions.name')
            ->get();

        return view('admin.role-permissions.index', compact('assignments'));
    }

    public function create(): View
    {
        return view('admin.role-permissions.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request));

        Role::findOrFail($validated['role_id'])
            ->givePermissionTo(Permission::findOrFail($validated['permission_id']));

        return $this->redirectWithStatus('Permissie aan rol gekoppeld.');
    }

    public function edit(string $rolePermission): View
    {
        [$roleId, $permissionId] = $this->parseKey($rolePermission);
        $role = Role::where('guard_name', 'web')->findOrFail($roleId);
        $permission = Permission::where('guard_name', 'web')->findOrFail($permissionId);
        abort_unless($role->hasPermissionTo($permission), 404);

        return view('admin.role-permissions.edit', [
            ...$this->formOptions(),
            'assignment' => (object) [
                'role_id' => $role->id,
                'permission_id' => $permission->id,
                'role_name' => $role->name,
                'permission_name' => $permission->name,
            ],
        ]);
    }

    public function update(Request $request, string $rolePermission): RedirectResponse
    {
        [$oldRoleId, $oldPermissionId] = $this->parseKey($rolePermission);
        $oldRole = Role::where('guard_name', 'web')->findOrFail($oldRoleId);
        $oldPermission = Permission::where('guard_name', 'web')->findOrFail($oldPermissionId);
        abort_unless($oldRole->hasPermissionTo($oldPermission), 404);

        $validated = $request->validate($this->rules($request, $oldRoleId, $oldPermissionId));
        $newRole = Role::where('guard_name', 'web')->findOrFail($validated['role_id']);
        $newPermission = Permission::where('guard_name', 'web')->findOrFail($validated['permission_id']);

        DB::transaction(function () use ($oldRole, $oldPermission, $newRole, $newPermission): void {
            $oldRole->revokePermissionTo($oldPermission);
            $newRole->givePermissionTo($newPermission);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $this->redirectWithStatus('Koppeling bijgewerkt.');
    }

    public function destroy(string $rolePermission): RedirectResponse
    {
        [$roleId, $permissionId] = $this->parseKey($rolePermission);
        $role = Role::where('guard_name', 'web')->findOrFail($roleId);
        $permission = Permission::where('guard_name', 'web')->findOrFail($permissionId);
        abort_unless($role->hasPermissionTo($permission), 404);

        $role->revokePermissionTo($permission);

        return $this->redirectWithStatus('Koppeling verwijderd.');
    }

    private function rules(Request $request, ?int $oldRoleId = null, ?int $oldPermissionId = null): array
    {
        $uniqueAssignment = Rule::unique('role_has_permissions', 'permission_id')
            ->where(fn ($query) => $query->where('role_id', $request->input('role_id')));

        if ($oldRoleId !== null && $oldPermissionId !== null) {
            $uniqueAssignment->ignore($oldPermissionId, 'permission_id');
        }

        return [
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'web')],
            'permission_id' => [
                'required',
                'integer',
                Rule::exists('permissions', 'id')->where('guard_name', 'web'),
                $uniqueAssignment,
            ],
        ];
    }

    private function formOptions(): array
    {
        return [
            'roles' => Role::where('guard_name', 'web')->orderBy('name')->get(),
            'permissions' => Permission::where('guard_name', 'web')->orderBy('name')->get(),
        ];
    }

    private function parseKey(string $key): array
    {
        abort_unless(preg_match('/^\d+-\d+$/', $key) === 1, 404);

        return array_map('intval', explode('-', $key));
    }

    private function redirectWithStatus(string $status): RedirectResponse
    {
        return redirect()->route('admin.role-permissions.index')->with('status', $status);
    }
}
