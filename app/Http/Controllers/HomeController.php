<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;

class HomeController extends Controller
{
    public function index()
    {
        $featuredItems = MenuItem::query()
            ->where('is_available', true)
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('home', compact('featuredItems'));
    }
}
