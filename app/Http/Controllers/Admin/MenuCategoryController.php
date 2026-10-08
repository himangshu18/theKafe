<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::withCount('items')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        MenuCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, MenuCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(MenuCategory $category)
    {
        $category->delete();

        return back()->with('success', 'Category removed.');
    }
}
