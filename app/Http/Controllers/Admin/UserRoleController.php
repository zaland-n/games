<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserRoleController extends Controller
{
    public function index(): View
    {
        $assignments = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->where('roles.guard_name', 'web')
            ->where('model_has_roles.model_type', User::class)
            ->select('model_has_roles.role_id', 'model_has_roles.model_id as user_id', 'roles.name as role_name', 'users.name as user_name', 'users.email')
            ->orderBy('users.email')
            ->orderBy('roles.name')
            ->get();

        return view('admin.user-roles.index', compact('assignments'));
    }

    public function create(): View
    {
        return view('admin.user-roles.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request));
        User::findOrFail($validated['user_id'])
            ->assignRole(Role::where('guard_name', 'web')->findOrFail($validated['role_id']));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $this->redirectWithStatus('Rol aan gebruiker gekoppeld.');
    }

    public function edit(string $userRole): View
    {
        [$userId, $roleId] = $this->parseKey($userRole);
        $user = User::findOrFail($userId);
        $role = Role::where('guard_name', 'web')->findOrFail($roleId);
        abort_unless($user->hasRole($role), 404);

        return view('admin.user-roles.edit', [
            ...$this->formOptions(),
            'assignment' => (object) [
                'user_id' => $user->id,
                'role_id' => $role->id,
                'user_name' => $user->name,
                'email' => $user->email,
                'role_name' => $role->name,
            ],
        ]);
    }

    public function update(Request $request, string $userRole): RedirectResponse
    {
        [$oldUserId, $oldRoleId] = $this->parseKey($userRole);
        $oldUser = User::findOrFail($oldUserId);
        $oldRole = Role::where('guard_name', 'web')->findOrFail($oldRoleId);
        abort_unless($oldUser->hasRole($oldRole), 404);

        $validated = $request->validate($this->rules($request, $oldUserId, $oldRoleId));
        $newUser = User::findOrFail($validated['user_id']);
        $newRole = Role::where('guard_name', 'web')->findOrFail($validated['role_id']);

        DB::transaction(function () use ($oldUser, $oldRole, $newUser, $newRole): void {
            $oldUser->removeRole($oldRole);
            $newUser->assignRole($newRole);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $this->redirectWithStatus('Koppeling bijgewerkt.');
    }

    public function destroy(string $userRole): RedirectResponse
    {
        [$userId, $roleId] = $this->parseKey($userRole);
        $user = User::findOrFail($userId);
        $role = Role::where('guard_name', 'web')->findOrFail($roleId);
        abort_unless($user->hasRole($role), 404);

        $user->removeRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $this->redirectWithStatus('Koppeling verwijderd.');
    }

    private function rules(Request $request, ?int $oldUserId = null, ?int $oldRoleId = null): array
    {
        $uniqueAssignment = Rule::unique('model_has_roles', 'role_id')
            ->where(fn ($query) => $query
                ->where('model_type', User::class)
                ->where('model_id', $request->input('user_id')));

        if ($oldUserId !== null && $oldRoleId !== null) {
            $uniqueAssignment->ignore($oldRoleId, 'role_id');
        }

        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'web'),
                $uniqueAssignment,
            ],
        ];
    }

    private function formOptions(): array
    {
        return [
            'users' => User::orderBy('email')->get(['id', 'name', 'email']),
            'roles' => Role::where('guard_name', 'web')->orderBy('name')->get(),
        ];
    }

    private function parseKey(string $key): array
    {
        abort_unless(preg_match('/^\d+-\d+$/', $key) === 1, 404);

        return array_map('intval', explode('-', $key));
    }

    private function redirectWithStatus(string $status): RedirectResponse
    {
        return redirect()->route('admin.user-roles.index')->with('status', $status);
    }
}
