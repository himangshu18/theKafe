<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_menu_item_with_explicit_food_type(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $category = MenuCategory::create([
            'name' => 'Momos',
            'slug' => 'momos',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.menu.store'), [
                'menu_category_id' => $category->id,
                'name' => 'Chicken Momo',
                'price' => '89.50',
                'food_type' => 'chicken',
                'is_available' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('menu_items', [
            'menu_category_id' => $category->id,
            'name' => 'Chicken Momo',
            'price' => '89.50',
            'food_type' => 'chicken',
            'is_available' => true,
        ]);
    }

    public function test_menu_item_rejects_prices_that_cannot_be_stored_as_cents(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $category = MenuCategory::create([
            'name' => 'Tea',
            'slug' => 'tea',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.menu.index'))
            ->post(route('admin.menu.store'), [
                'menu_category_id' => $category->id,
                'name' => 'Invalid price tea',
                'price' => '100000000.00',
                'food_type' => 'veg',
            ])
            ->assertRedirect(route('admin.menu.index'))
            ->assertSessionHasErrors('price');

        $this->assertFalse(MenuItem::query()->where('name', 'Invalid price tea')->exists());
    }

    public function test_new_menu_item_is_unavailable_when_availability_checkbox_is_clear(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $category = MenuCategory::create([
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.menu.store'), [
                'menu_category_id' => $category->id,
                'name' => 'Temporarily unavailable coffee',
                'price' => '39.00',
                'food_type' => 'veg',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('menu_items', [
            'name' => 'Temporarily unavailable coffee',
            'is_available' => false,
        ]);
    }
}
