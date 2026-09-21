<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('tickets')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'color' => 'nullable|string|max:20',
        ]);

        Category::create([
            'name' => $data['name'],
            'color' => $data['color'] ?? '#3B82F6',
        ]);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'color' => 'nullable|string|max:20',
        ]);

        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->tickets()->detach();
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
