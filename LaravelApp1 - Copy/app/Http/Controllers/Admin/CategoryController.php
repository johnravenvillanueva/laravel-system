<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{

    public function index()
    {
        // With product counts so we can show "N products" per category
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }


    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(int $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:categories,name,' . $id . ',category_id'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }


    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);

        // Prevent deleting categories that still have products (FK restrict)
        try {
            $category->delete();
        } catch (QueryException $e) {
            return back()->with('error',
                'Cannot delete this category — it still has products. ' .
                'Move or delete those products first.'
            );
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }
}