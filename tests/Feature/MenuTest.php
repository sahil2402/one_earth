<?php

namespace Tests\Feature;

use App\Constants\OwnerCredentials;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_parent_and_child_menus(): void
    {
        $this->withSession([
            'authenticated_role' => OwnerCredentials::ROLE,
            'authenticated_email' => OwnerCredentials::EMAIL,
        ])->post(route('menu.store'), [
            'name' => 'Operations',
            'menu_type' => 'parent',
        ])->assertRedirect(route('menu.create'));

        $parent = Menu::firstOrFail();

        $this->withSession([
            'authenticated_role' => OwnerCredentials::ROLE,
            'authenticated_email' => OwnerCredentials::EMAIL,
        ])->post(route('menu.store'), [
            'name' => 'Bookings',
            'menu_type' => 'child',
            'parent_id' => $parent->id,
        ])->assertRedirect(route('menu.create'));

        $this->assertDatabaseHas('menus', ['name' => 'Operations', 'parent_id' => null]);
        $this->assertDatabaseHas('menus', ['name' => 'Bookings', 'parent_id' => $parent->id]);
    }

    public function test_settings_is_available_as_a_default_parent_menu(): void
    {
        $this->withSession([
            'authenticated_role' => OwnerCredentials::ROLE,
            'authenticated_email' => OwnerCredentials::EMAIL,
        ])->get(route('menu.create'))
            ->assertOk()
            ->assertSee('Settings');

        $this->assertDatabaseHas('menus', [
            'name' => 'Settings',
            'slug' => 'settings',
            'parent_id' => null,
        ]);
    }

    public function test_saved_menus_are_displayed_in_the_sidebar(): void
    {
        Menu::create(['name' => 'Operations', 'slug' => 'operations']);

        $this->withSession([
            'authenticated_role' => OwnerCredentials::ROLE,
            'authenticated_email' => OwnerCredentials::EMAIL,
        ])->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Operations');
    }

    public function test_created_child_menu_has_a_working_blank_page_route(): void
    {
        $parent = Menu::create(['name' => 'Settings', 'slug' => 'settings']);
        $menu = Menu::create(['name' => 'Role and Permission', 'slug' => 'role-and-permission', 'parent_id' => $parent->id]);

        $this->withSession([
            'authenticated_role' => OwnerCredentials::ROLE,
            'authenticated_email' => OwnerCredentials::EMAIL,
        ])->get(route('menus.show', $menu))
            ->assertOk()
            ->assertSee('Role and Permission');
    }
}
