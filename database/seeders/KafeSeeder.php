<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KafeSeeder extends Seeder
{
    public function run(): void
    {
        $ownerPassword = config('kafe.owner_password');
        if (! is_string($ownerPassword) || mb_strlen($ownerPassword) < 12) {
            throw new \RuntimeException('Set KAFE_OWNER_PASSWORD to a unique password of at least 12 characters before seeding.');
        }

        User::updateOrCreate(
            ['email' => 'owner@thekafe.com'],
            [
                'name' => 'Cafe Owner',
                'password' => $ownerPassword,
                'is_admin' => true,
                'admin_role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        $catalog = [
            'Starters' => [
                ['Chicken Nuggets (5 pcs)', 69],
                ['Chicken Wings (5 pcs)', 69],
                ['Air Fried Chicken Leg (2 pcs)', 79],
                ['Nachos with Cheese Dip', 99],
                ['Cheesy Delight Nachos', 129],
            ],
            'Garlic Bread & Toasties' => [
                ['Cheese Garlic Bread', 89],
                ['Garlic Corn Delight', 99],
                ['Penne Garlic Pleasure', 119],
                ['Korean Spicy Paneer Toastie', 99],
            ],
            'Burgers' => [
                ['Classic Burger', 59],
                ['Barbeque Burger', 79],
                ['Double Tikki Burger', 89],
                ['Tandoori Chicken Burger', 99],
                ['BBQ Chicken Burger', 99],
                ['Grilled Paneer Burger', 119],
                ['Mexican Jumbo Burger', 129],
                ['Korean Burger', 119],
                ['Korean Chicken Burger', 129],
            ],
            'Fries' => [
                ['Salted French Fries (with Cheese Dip)', 79],
                ['Peri Peri Fries (with Cheese Dip)', 99],
                ['Cheese Masala Fries (with Cheese Dip)', 99],
                ['The Kafe Special Loaded Fries (Chicken)', 169],
                ['Korean Fries (Chicken)', 159],
            ],
            'Wraps' => [
                ['Classic Veg Wrap', 129],
                ['Grilled Paneer Wrap', 149],
                ['Tandoori Chicken Wrap', 159],
                ['BBQ Chicken Wrap', 169],
            ],
            'Kulhad Specials' => [
                ['Melt-in Kulhad Momo', 149],
                ['Melt-in Kulhad Pizza', 169],
                ['Melt-in Kulhad Maggi', 139],
            ],
            'Pizza' => [
                ['Margherita', 89],
                ['Cheese Corn Pizza (Kids Special)', 119],
                ['Crispo (Thinnest Pizza)', 139],
                ['Veggie Carnival Pizza', 169],
                ['Cheese Burst Pizza', 199],
                ['Paneer Barbeque Pizza', 199],
                ['Tandoori Chicken Pizza', 219],
                ['Barbeque Chicken Pizza', 209],
                ['Chicken Sausage Pizza', 239],
                ['Korean Pizza', 179],
            ],
            'Sandwiches' => [
                ['Veggie Grilled Sandwich', 109],
                ['Melt in Chilli Grilled Sandwich', 129],
                ['Mexican Marvel Sandwich', 159],
                ['Corn Crusher Sandwich', 149],
                ['Chicken Sandwich', 169],
                ['Paneer Paradise Sandwich', 169],
            ],
            'Pasta' => [
                ['Rosa Tella Pasta (Pink Pasta)', 149],
                ['Alfredo Pasta (White Sauce)', 149],
                ['Angry Pasta (Red Sauce)', 149],
            ],
            'Main Course' => [
                ['Chicken Thukpa', 129],
                ['Chicken Noodles', 99],
                ['Egg Noodles', 79],
                ['Veg Noodles', 79],
                ['Chicken Fried Rice', 99],
                ['Egg Fried Rice', 79],
                ['Veg Fried Rice', 79],
                ['Paneer Fried Rice', 89],
                ['Chicken Sizzler', 249],
                ['Paneer Sizzler', 249],
            ],
            'Momos' => [
                ['Steamed Momo', 79],
                ['Fried Momo', 99],
                ['Kurkure Momo', 129],
                ['Molten Cheese Momo', 169],
            ],
            'Maggi' => [
                ['Double Masala Maggi', 49],
                ['Desi Dynamite Maggi', 69],
                ['Veg Masala Maggi', 79],
                ['Cheesy Chaska Maggi', 99],
            ],
            'Milkshakes' => [
                ['Rose Delight Shake', 99],
                ['Strawberry Shake', 99],
                ['Oreo Chocolate Shake', 99],
                ['Kitkat Chocolate Shake', 119],
                ['Brownie Blast Shake', 129],
            ],
            'Cold Coffee' => [
                ['Elite Cold Coffee', 59],
                ['Dark Storm Cold Coffee', 69],
                ['Mocha Cold Coffee', 99],
            ],
            'Hot Beverages' => [
                ['Masala Chai', 19],
                ['Hot Coffee', 29],
                ['Hot Chocolate', 49],
            ],
            'Ice Tea' => [
                ['Lemon Ice Tea', 79],
                ['Cranberry Ice Tea', 79],
                ['Peach Ice Tea', 79],
            ],
            'Mojito' => [
                ['Black Current Mojito', 49],
                ['Guava Groove Mojito', 49],
                ['Strawberry Mojito', 49],
                ['Aam Twist Mojito', 49],
                ['Punchy Pineapple Mojito', 49],
                ['Litchious Mojito', 49],
                ['Mint Mojito', 49],
                ['Orange Oasis Mojito', 49],
                ['Blue Beast Mojito', 49],
                ['Petal Pusher Mojito', 49],
                ['Cranberry Mojito', 59],
                ['Peach Mojito', 59],
            ],
            'Desserts' => [
                ['Cupcake', 49],
                ['Sizzling Brownie', 139],
                ['Dirt-Y Delight Pie', 159],
            ],
        ];

        MenuCategory::query()
            ->whereIn('name', ['Chai & Coffee', 'Snacks', 'Meals'])
            ->update(['is_active' => false]);

        $items = [];
        $categorySortOrder = 0;
        foreach ($catalog as $categoryName => $menuItems) {
            $slug = Str::slug($categoryName);
            $category = MenuCategory::withTrashed()->firstOrNew(['slug' => $slug]);
            $category->fill([
                'name' => $categoryName,
                'sort_order' => $categorySortOrder++,
                'is_active' => true,
            ]);
            if ($category->trashed()) {
                $category->restore();
            } else {
                $category->save();
            }

            foreach ($menuItems as $sortOrder => [$name, $price]) {
                $item = MenuItem::withTrashed()->firstOrNew([
                    'menu_category_id' => $category->id,
                    'name' => $name,
                ]);
                $item->fill([
                    'description' => null,
                    'price' => number_format((int) $price, 2, '.', ''),
                    'food_type' => $this->foodType($name),
                    'is_available' => true,
                    'is_featured' => $sortOrder === 0,
                    'sort_order' => $sortOrder,
                ]);
                if ($item->trashed()) {
                    $item->restore();
                } else {
                    $item->save();
                }
                $items[$name] = $item;
            }
        }

        $this->seedCombo($items, 'Meal for Two', 499, 'meal', [
            $this->group('Choose any pizza', 1, 1, $this->namesForCategory($catalog, 'Pizza')),
            $this->group('Classic Veg Wrap', 1, 1, ['Classic Veg Wrap']),
            $this->group('Cheese Garlic Bread', 1, 1, ['Cheese Garlic Bread']),
            $this->group('Salted French Fries', 1, 1, ['Salted French Fries (with Cheese Dip)']),
            $this->group('Choose any two Mojitos', 2, 2, $this->namesForCategory($catalog, 'Mojito')),
        ]);
        $this->seedCombo($items, 'Meal for Three', 599, 'meal', [
            $this->group('Choose any pizza', 1, 1, $this->namesForCategory($catalog, 'Pizza')),
            $this->group('Classic Veg Wrap', 1, 1, ['Classic Veg Wrap']),
            $this->group('Cheese Garlic Bread', 1, 1, ['Cheese Garlic Bread']),
            $this->group('Salted French Fries', 1, 1, ['Salted French Fries (with Cheese Dip)']),
            $this->group('Choose any three Mojitos', 3, 3, $this->namesForCategory($catalog, 'Mojito')),
            $this->group('Classic Burger', 1, 1, ['Classic Burger']),
        ]);
        $this->seedCombo($items, 'Meal for Family', 699, 'meal', [
            $this->group('Veggie Carnival Pizza', 1, 1, ['Veggie Carnival Pizza']),
            $this->group('Margherita Pizza', 1, 1, ['Margherita']),
            $this->group('Mexican Burger', 1, 1, ['Mexican Jumbo Burger']),
            $this->group('Cheese Garlic Bread', 1, 1, ['Cheese Garlic Bread']),
            $this->group('Salted French Fries', 1, 1, ['Salted French Fries (with Cheese Dip)']),
            $this->group('Choose any four Mojitos', 4, 4, $this->namesForCategory($catalog, 'Mojito')),
        ]);

        $comboOffers = [
            ['Veggie Carnival + Garlic Bread + Mojito', 249, [
                $this->group('Veggie Carnival Pizza', 1, 1, ['Veggie Carnival Pizza']),
                $this->group('Cheese Garlic Bread', 1, 1, ['Cheese Garlic Bread']),
                $this->group('Choose a Mojito', 1, 1, $this->namesForCategory($catalog, 'Mojito')),
            ]],
            ['Molten Cheese Momo + Mojito', 199, [
                $this->group('Molten Cheese Momo', 1, 1, ['Molten Cheese Momo']),
                $this->group('Choose a Mojito', 1, 1, $this->namesForCategory($catalog, 'Mojito')),
            ]],
            ['Chicken Burger + Mojito', 119, [
                $this->group('Choose a Chicken Burger', 1, 1, ['Tandoori Chicken Burger', 'BBQ Chicken Burger', 'Korean Chicken Burger']),
                $this->group('Choose a Mojito', 1, 1, $this->namesForCategory($catalog, 'Mojito')),
            ]],
            ['Any Kulhad + Mojito', 179, [
                $this->group('Choose a Kulhad Special', 1, 1, $this->namesForCategory($catalog, 'Kulhad Specials')),
                $this->group('Choose a Mojito', 1, 1, $this->namesForCategory($catalog, 'Mojito')),
            ]],
            ['Any Pasta + Cold Coffee', 179, [
                $this->group('Choose a Pasta', 1, 1, $this->namesForCategory($catalog, 'Pasta')),
                $this->group('Choose a Cold Coffee', 1, 1, $this->namesForCategory($catalog, 'Cold Coffee')),
            ]],
            ['Crispo Pizza + Cold Coffee', 159, [
                $this->group('Crispo Pizza', 1, 1, ['Crispo (Thinnest Pizza)']),
                $this->group('Choose a Cold Coffee', 1, 1, $this->namesForCategory($catalog, 'Cold Coffee')),
            ]],
            ['Two Elite Cold Coffees', 109, [
                $this->group('Elite Cold Coffee', 1, 1, ['Elite Cold Coffee'], 2),
            ]],
            ['Any Three Mojitos', 109, [
                $this->group('Choose any three Mojitos', 3, 3, $this->namesForCategory($catalog, 'Mojito')),
            ]],
            ['Any Grilled Sandwich + Cold Coffee', 169, [
                $this->group('Choose a Grilled Sandwich', 1, 1, ['Veggie Grilled Sandwich', 'Melt in Chilli Grilled Sandwich', 'Paneer Paradise Sandwich']),
                $this->group('Choose a Cold Coffee', 1, 1, $this->namesForCategory($catalog, 'Cold Coffee')),
            ]],
            ['Classic Burger + Fries + Mojito', 209, [
                $this->group('Classic Burger', 1, 1, ['Classic Burger']),
                $this->group('Salted French Fries', 1, 1, ['Salted French Fries (with Cheese Dip)']),
                $this->group('Choose a Mojito', 1, 1, $this->namesForCategory($catalog, 'Mojito')),
            ]],
            ['Any Chicken Pizza + Fries + Mojito', 269, [
                $this->group('Choose a Chicken Pizza', 1, 1, ['Tandoori Chicken Pizza', 'Barbeque Chicken Pizza', 'Chicken Sausage Pizza']),
                $this->group('Salted French Fries', 1, 1, ['Salted French Fries (with Cheese Dip)']),
                $this->group('Choose a Mojito', 1, 1, $this->namesForCategory($catalog, 'Mojito')),
            ]],
        ];

        foreach ($comboOffers as [$name, $price, $groups]) {
            $this->seedCombo($items, $name, $price, 'offer', $groups);
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return list<string>
     */
    private function namesForCategory(array $catalog, string $category): array
    {
        return array_map(fn (array $item): string => $item[0], $catalog[$category]);
    }

    /**
     * @param  list<string>  $itemNames
     * @return array{name: string, min_select: int, max_select: int, included_quantity: int, items: list<string>}
     */
    private function group(string $name, int $minSelect, int $maxSelect, array $itemNames, int $includedQuantity = 1): array
    {
        return [
            'name' => $name,
            'min_select' => $minSelect,
            'max_select' => $maxSelect,
            'included_quantity' => $includedQuantity,
            'items' => $itemNames,
        ];
    }

    /**
     * @param  array<string, MenuItem>  $items
     * @param  list<array{name: string, min_select: int, max_select: int, included_quantity: int, items: list<string>}>  $groups
     */
    private function seedCombo(array $items, string $name, int $price, string $kind, array $groups): void
    {
        $slug = Str::slug($name);
        $combo = Combo::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => null,
                'price' => number_format($price, 2, '.', ''),
                'kind' => $kind,
                'is_active' => true,
            ]
        );
        $combo->groups()->delete();

        foreach ($groups as $sortOrder => $groupData) {
            $group = $combo->groups()->create([
                'name' => $groupData['name'],
                'min_select' => $groupData['min_select'],
                'max_select' => $groupData['max_select'],
                'included_quantity' => $groupData['included_quantity'],
                'sort_order' => $sortOrder,
            ]);

            $eligibleItems = [];
            foreach ($groupData['items'] as $itemName) {
                if (! isset($items[$itemName])) {
                    throw new \RuntimeException(sprintf('The combo item "%s" is missing from the catalog.', $itemName));
                }
                $eligibleItems[$items[$itemName]->id] = ['price_adjustment' => '0.00'];
            }
            $group->eligibleItems()->sync($eligibleItems);
        }
    }

    private function foodType(string $name): string
    {
        if (preg_match('/\b(chicken|sausage)\b/i', $name)) {
            return 'chicken';
        }

        if (preg_match('/\begg\b/i', $name)) {
            return 'egg';
        }

        return 'veg';
    }
}
