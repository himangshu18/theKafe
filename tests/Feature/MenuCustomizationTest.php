<?php

namespace Tests\Feature;

use App\Models\AddonGroup;
use App\Models\AddonOption;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_saves_item_variations_addons_and_availability_hours(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $item = $this->createMenuItem('Cold Coffee');

        $response = $this->actingAs($admin)
            ->put(route('admin.menu.customize.update', $item), [
                'variations' => [[
                    'name' => 'Large',
                    'price' => '95.00',
                    'sort_order' => 0,
                    'is_available' => '1',
                ]],
                'addon_groups' => [[
                    'name' => 'Extras',
                    'min_select' => 0,
                    'max_select' => 1,
                    'options' => [[
                        'name' => 'Whipped cream',
                        'price_delta' => '10.00',
                        'is_available' => '1',
                    ]],
                ]],
                'schedules' => [[
                    'day_of_week' => 1,
                    'starts_at' => '09:00',
                    'ends_at' => '18:00',
                    'is_available' => '1',
                ]],
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('menu_variations', [
            'menu_item_id' => $item->id,
            'name' => 'Large',
            'price' => '95.00',
        ]);
        $this->assertDatabaseHas('addon_options', [
            'name' => 'Whipped cream',
            'price_delta' => '10.00',
        ]);
        $this->assertDatabaseHas('menu_item_availability_schedules', [
            'menu_item_id' => $item->id,
            'starts_at' => '09:00:00',
        ]);
    }

    public function test_admin_cannot_attach_a_variation_from_another_item(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $item = $this->createMenuItem('Cold Coffee');
        $otherItem = $this->createMenuItem('Hot Coffee');
        $otherVariation = $otherItem->variations()->create([
            'name' => 'Large',
            'price' => '80.00',
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.menu.customize.edit', $item))
            ->put(route('admin.menu.customize.update', $item), [
                'variations' => [[
                    'id' => $otherVariation->id,
                    'name' => 'Hijacked variation',
                    'price' => '1.00',
                ]],
            ]);

        $response->assertRedirect(route('admin.menu.customize.edit', $item));
        $response->assertSessionHasErrors('variations');
        $this->assertSame('Large', $otherVariation->refresh()->name);
    }

    public function test_admin_can_update_an_existing_addon_group_with_form_string_ids(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $item = $this->createMenuItem('Cold Coffee');
        $group = AddonGroup::create([
            'name' => 'Extras',
            'min_select' => 0,
            'max_select' => 1,
            'is_required' => false,
            'is_active' => true,
        ]);
        $option = AddonOption::create([
            'addon_group_id' => $group->id,
            'name' => 'Whipped cream',
            'price_delta' => '10.00',
            'is_available' => true,
        ]);
        $item->addonGroups()->attach($group);

        $this->actingAs($admin)
            ->put(route('admin.menu.customize.update', $item), [
                'addon_groups' => [[
                    'id' => (string) $group->id,
                    'name' => 'Extras updated',
                    'min_select' => '0',
                    'max_select' => '1',
                    'options' => [[
                        'id' => (string) $option->id,
                        'name' => 'Whipped cream',
                        'price_delta' => '12.00',
                        'is_available' => '1',
                    ]],
                ]],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('Extras updated', $group->refresh()->name);
        $this->assertSame('12.00', $option->refresh()->price_delta);
    }

    public function test_admin_uploads_menu_images_to_public_storage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $item = $this->createMenuItem('Mango Mojito');

        $this->actingAs($admin)
            ->put(route('admin.menu.customize.update', $item), [
                'images' => [UploadedFile::fake()->image('mango-mojito.jpg')],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $image = MenuItemImage::query()->where('menu_item_id', $item->id)->firstOrFail();
        Storage::disk('public')->assertExists($image->image_path);
    }

    private function createMenuItem(string $name): MenuItem
    {
        $category = MenuCategory::create([
            'name' => $name.' category',
            'slug' => str()->slug($name.' category'),
            'is_active' => true,
        ]);

        return MenuItem::create([
            'menu_category_id' => $category->id,
            'name' => $name,
            'price' => '75.00',
            'is_available' => true,
        ]);
    }
}
