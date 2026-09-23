<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $data = [
            'name' => $request->name,
            'slug' => $slug,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori başarıyla eklendi.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $data = [
            'name' => $request->name,
            'slug' => $slug,
        ];

        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori başarıyla güncellendi.');
    }

    public function destroy(Category $category)
    {
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori başarıyla silindi.');
    }
}