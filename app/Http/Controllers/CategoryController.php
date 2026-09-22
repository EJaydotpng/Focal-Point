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

    // Used by the board's "+ Category" button to load the create-category form into the modal.
    public function create(Request $request)
    {
        if ($this->wantsPartial($request)) {
            return view('categories._create_modal_content');
        }

        return redirect()->route('categories.index');
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

        if ($this->wantsPartial($request)) {
            return response()->json(['ok' => true]);
        }

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

    // True when the request came from our own fetch() calls (board modal),
    // as opposed to a normal browser page load / direct link.
    protected function wantsPartial(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
