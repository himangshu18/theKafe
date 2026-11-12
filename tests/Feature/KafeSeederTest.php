<?php

namespace Tests\Feature;

use App\Models\Combo;
use App\Models\ComboGroup;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Database\Seeders\KafeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KafeSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseeding_preserves_a_single_catalog_and_combo_structure(): void
    {
        config(['kafe.owner_password' => 'local-test-owner-password']);

        $this->seed(KafeSeeder::class);
        $countsAfterFirstSeed = [
            MenuCategory::query()->count(),
            MenuItem::query()->count(),
            Combo::query()->count(),
            ComboGroup::query()->count(),
        ];

        $this->seed(KafeSeeder::class);
        $owner = User::query()->where('email', 'owner@thekafe.com')->firstOrFail();

        $this->assertSame($countsAfterFirstSeed, [
            MenuCategory::query()->count(),
            MenuItem::query()->count(),
            Combo::query()->count(),
            ComboGroup::query()->count(),
        ]);
        $this->assertTrue(Hash::check('local-test-owner-password', $owner->password));
        $this->assertDatabaseHas('menu_categories', [
            'name' => 'Starters',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('menu_items', [
            'name' => 'Chicken Nuggets (5 pcs)',
            'food_type' => 'chicken',
        ]);
        $this->assertDatabaseHas('combos', [
            'slug' => 'meal-for-two',
            'price' => '499.00',
        ]);
    }
}
