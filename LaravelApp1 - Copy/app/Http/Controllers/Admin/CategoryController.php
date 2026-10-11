<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => DemoData::categories()]);
    }

    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:50'],
            'description' => ['nullable','string'],
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(int $id)
    {
        $category = DemoData::categories()->firstWhere('id', $id) ?? DemoData::categories()->first();
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => ['required','string','max:50'],
            'description' => ['nullable','string'],
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(int $id)
    {
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}