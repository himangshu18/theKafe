<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KafeSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->where('email', 'owner@thekafe.local')->delete();

        User::updateOrCreate(
            ['email' => 'owner@thekafe.com'],
            [
                'name' => 'Cafe Owner',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            ['name' => 'Chai & Coffee', 'items' => [
                ['name' => 'Masala Chai', 'price' => 30, 'description' => 'Kadak cutting chai', 'featured' => true],
                ['name' => 'Filter Coffee', 'price' => 50, 'description' => 'South-style strong brew', 'featured' => true],
                ['name' => 'Cold Coffee', 'price' => 80, 'description' => 'Creamy blended', 'featured' => false],
            ]],
            ['name' => 'Snacks', 'items' => [
                ['name' => 'Veg Samosa (2 pc)', 'price' => 40, 'description' => 'Crispy with chutney', 'featured' => true],
                ['name' => 'Kachori Plate', 'price' => 60, 'description' => 'Rajasthani style', 'featured' => true],
                ['name' => 'French Fries', 'price' => 90, 'description' => 'Salted & crispy', 'featured' => false],
            ]],
            ['name' => 'Meals', 'items' => [
                ['name' => 'Veg Thali', 'price' => 180, 'description' => 'Roti, dal, sabzi, rice', 'featured' => true],
                ['name' => 'Paratha Plate', 'price' => 120, 'description' => 'Stuffed paratha with curd', 'featured' => false],
            ]],
        ];

        foreach ($categories as $index => $block) {
            $category = MenuCategory::create([
                'name' => $block['name'],
                'slug' => Str::slug($block['name']),
                'sort_order' => $index,
                'is_active' => true,
            ]);

            foreach ($block['items'] as $i => $item) {
                MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'is_available' => true,
                    'is_featured' => $item['featured'],
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
