<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        $category = Category::create($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]));
        return response()->json(['data' => $category], 201);
    }

    public function show(Category $category)
    {
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]));
        return response()->json(['data' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
