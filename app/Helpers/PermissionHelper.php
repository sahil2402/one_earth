<?php

namespace App\Helpers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;

class PermissionHelper
{
    /**
     * Check if the current authenticated role has permission for a specific action on a menu slug.
     * Actions: 'view', 'create', 'update', 'delete'
     */
    public static function check(string $menuSlug, string $action): bool
    {
        $roleName = session('authenticated_role');
        if (!$roleName) {
            return false;
        }

        // Owner has absolute control/master access to everything
        if (strtolower($roleName) === 'owner') {
            return true;
        }

        // Get the role from Database
        $role = Role::where('name', $roleName)->where('is_active', true)->first();
        if (!$role) {
            return false;
        }

        // Find the menu
        $menu = Menu::where('slug', $menuSlug)->first();
        if (!$menu) {
            return false;
        }

        // Find the permission record
        $permission = RoleMenuPermission::where('role_id', $role->id)
            ->where('menu_id', $menu->id)
            ->first();

        if (!$permission) {
            return false;
        }

        return match ($action) {
            'view' => (bool)$permission->can_view,
            'create' => (bool)$permission->can_create,
            'update' => (bool)$permission->can_update,
            'delete' => (bool)$permission->can_delete,
            default => false,
        };
    }

    /**
     * Filter a navigation tree or collection of menus based on current user's view permission.
     */
    public static function filterMenus($menus)
    {
        $roleName = session('authenticated_role');
        if (!$roleName) {
            return collect();
        }

        if (strtolower($roleName) === 'owner') {
            return $menus;
        }

        return $menus->filter(function ($menu) {
            // Check view permission for parent menu
            $hasView = self::check($menu->slug, 'view');
            
            // If the menu has children, recursively filter them
            if ($menu->relationLoaded('children') && $menu->children->isNotEmpty()) {
                $menu->setRelation('children', self::filterMenus($menu->children));
                
                // If the parent menu doesn't have view permission but has visible children,
                // we should still keep the parent menu so children are accessible.
                if (!$hasView && $menu->children->isNotEmpty()) {
                    $hasView = true;
                }
            }

            return $hasView;
        });
    }
}
