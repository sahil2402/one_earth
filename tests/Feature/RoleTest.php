<?php

namespace Tests\Feature;

use App\Constants\OwnerCredentials;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_a_role_with_module_permissions(): void
    {
        $menu = Menu::create(['name' => 'Bookings', 'slug' => 'bookings']);

        $this->withSession([
            'authenticated_role' => OwnerCredentials::ROLE,
            'authenticated_email' => OwnerCredentials::EMAIL,
        ])->post(route('roles.store'), [
            'name' => 'Manager',
            'is_active' => '1',
            'permissions' => [$menu->id => ['create' => '1', 'update' => '1']],
        ])->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'Manager', 'is_active' => true]);
        $this->assertDatabaseHas('role_menu_permissions', ['menu_id' => $menu->id, 'can_create' => true, 'can_update' => true, 'can_delete' => false]);
    }
}
