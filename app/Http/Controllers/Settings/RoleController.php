<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        if (!\App\Helpers\PermissionHelper::check('roleandpermission', 'view')) {
            abort(403, 'Unauthorized.');
        }

        return view('roles.index', [
            'role' => session('authenticated_role'),
            'email' => session('authenticated_email'),
            'navbarTitle' => 'Role & Permission',
            'menus' => Menu::query()->orderBy('name')->get(),
            'roles' => Role::query()->with('permissions')->latest()->get(),
            'editingRole' => $request->filled('edit') ? Role::with('permissions')->findOrFail($request->integer('edit')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('roleandpermission', 'create')) {
            abort(403, 'Unauthorized.');
        }

        $role = Role::create($this->validatedRole($request) + [
            'created_by' => $request->session()->get('authenticated_email'),
        ]);
        $this->syncPermissions($role, $request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('roleandpermission', 'update')) {
            abort(403, 'Unauthorized.');
        }

        $role->update($this->validatedRole($request, $role) + [
            'updated_by' => $request->session()->get('authenticated_email'),
        ]);
        $role->permissions()->delete();
        $this->syncPermissions($role, $request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (!\App\Helpers\PermissionHelper::check('roleandpermission', 'delete')) {
            abort(403, 'Unauthorized.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function toggleStatus(Request $request, Role $role): JsonResponse
    {
        if (!\App\Helpers\PermissionHelper::check('roleandpermission', 'update')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $role->update($validated + [
            'updated_by' => $request->session()->get('authenticated_email'),
        ]);

        return response()->json([
            'message' => 'Role and Permission updated successfully',
            'is_active' => $role->is_active,
        ]);
    }

    private function validatedRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*.view' => ['nullable', 'boolean'],
            'permissions.*.create' => ['nullable', 'boolean'],
            'permissions.*.update' => ['nullable', 'boolean'],
            'permissions.*.delete' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function syncPermissions(Role $role, array $permissions): void
    {
        $menuIds = Menu::query()->whereIn('id', array_keys($permissions))->pluck('id')->all();

        foreach ($menuIds as $menuId) {
            $permission = $permissions[$menuId] ?? [];
            $role->permissions()->create([
                'menu_id' => $menuId,
                'can_view' => !empty($permission['view']),
                'can_create' => !empty($permission['create']),
                'can_update' => !empty($permission['update']),
                'can_delete' => !empty($permission['delete']),
            ]);
        }
    }
}
