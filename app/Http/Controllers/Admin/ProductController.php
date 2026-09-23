<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index() 
    {
        $products = Product::with(['category', 'group'])->latest()->get();
                    
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $productGroups = ProductGroup::all(); 
                                    
        return view('admin.products.create', compact('categories', 'productGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'product_group_id' => 'nullable',
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'price'            => 'required|numeric|min:0',
            'shipping_price'   => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'color'            => 'nullable|string|max:50',
            'colorRGB'         => 'nullable|string|max:50',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'size' => 'nullable|string|max:100',
        ]);

        $data = $request->except(['image', 'is_main', 'new_group_name']);
        
        $data['shipping_price'] = $request->shipping_price ?? 0.00;
        $data['is_main'] = $request->has('is_main');
        
        if ($request->filled('new_group_name')) {
            $group = ProductGroup::create(['name' => $request->new_group_name]);
            $data['product_group_id'] = $group->id;
        } else {
            $data['product_group_id'] = $request->filled('product_group_id') ? $request->product_group_id : null;
        }

        $slugBase = Str::slug($request->name);
        $colorSlug = $request->color ? '-' . Str::slug($request->color) : '';
        $data['slug'] = $request->slug ? Str::slug($request->slug) : ($slugBase . $colorSlug . '-' . uniqid());

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/' . $imageName;
        }

        if ($data['is_main'] && !empty($data['product_group_id'])) {
            Product::where('product_group_id', $data['product_group_id'])->update(['is_main' => false]);
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Ürün başarıyla eklendi.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $productGroups = ProductGroup::all();
        return view('admin.products.edit', compact('product', 'categories', 'productGroups'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'product_group_id' => 'nullable',
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'price'            => 'required|numeric|min:0',
            'shipping_price'   => 'nullable|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'color'            => 'nullable|string|max:50',
            'colorRGB'         => 'nullable|string|max:50',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'size' => 'nullable|string|max:100',
        ]);

        $data = $request->except(['image', 'is_main']);
        $data['shipping_price'] = $request->shipping_price ?? 0.00;
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $data['is_main'] = $request->has('is_main');
        $data['product_group_id'] = $request->filled('product_group_id') ? $request->product_group_id : null;

        if ($data['is_main'] && $data['product_group_id']) {
            Product::where('product_group_id', $data['product_group_id'])->update(['is_main' => false]);
        }

        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/' . $imageName;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Ürün silindi.');
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        if (request()->ajax()) {
            return view('product.partials.detail', compact('product'));
        }

        return view('admin.products.show', compact('product'));
    }

    public function showDetail($id)
    {
        $product = Product::findOrFail($id);
        return view('product.partials.detail', compact('product'));
    }
}