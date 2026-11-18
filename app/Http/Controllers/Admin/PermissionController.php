<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): View
    {
        return view('admin.permissions.index', [
            'permissions' => Permission::where('guard_name', 'web')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->where('guard_name', 'web')],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.permissions.index')->with('status', 'Permissie aangemaakt.');
    }

    public function edit(Permission $permission): View
    {
        abort_unless($permission->guard_name === 'web', 404);

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        abort_unless($permission->guard_name === 'web', 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->where('guard_name', 'web')->ignore($permission->id),
            ],
        ]);

        $permission->update(['name' => $validated['name']]);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')->with('status', 'Permissie bijgewerkt.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_unless($permission->guard_name === 'web', 404);

        $permission->delete();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')->with('status', 'Permissie verwijderd.');
    }
}
