<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->orderBy('sort_order')->get();
        $categories = MenuCategory::orderBy('sort_order')->get();

        return view('admin.menu.index', compact('items', 'categories'));
    }

    public function store(StoreMenuItemRequest $request)
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu', 'public');
        }

        $data['is_available'] = $request->boolean('is_available', true);
        $data['is_featured'] = $request->boolean('is_featured', false);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        MenuItem::create($data);

        return back()->with('success', 'Menu item added.');
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem)
    {
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu', 'public');
        }

        $data['is_available'] = $request->boolean('is_available');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? $menuItem->sort_order;

        $menuItem->update($data);

        return back()->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }
        $menuItem->delete();

        return back()->with('success', 'Menu item removed.');
    }
}
