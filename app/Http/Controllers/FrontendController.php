<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::select('id', 'name')->get();
        $products = Product::with('category')->latest()->take(8)->get();
        
        return view('frontend.home', compact('products', 'categories'));
    }

    public function products(Request $request)
    {
        $categories = Category::all();
        $allColors = Product::whereNotNull('colorRGB')
            ->where('colorRGB', '!=', '')
            ->distinct()
            ->pluck('colorRGB');

        $query = Product::query();

        if ($request->has('categories') && is_array($request->categories)) {
            $filtered = array_filter($request->categories);
            if (!empty($filtered)) {
                $query->whereIn('category_id', $filtered);
            }
        }

        if ($request->has('colors') && is_array($request->colors)) {
            $filteredColors = array_filter($request->colors);
            if (!empty($filteredColors)) {
                $query->whereIn('colorRGB', $filteredColors);
            }
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            Log::info('Filtre sonucu ürün bulunamadı. Query: ' . $query->toSql());
        }

        $products = $products->unique('product_group_id')->values();

        if ($request->ajax()) {
            return view('frontend.product-list', compact('products'))->render();
        }

        return view('frontend.products', compact('categories', 'products', 'allColors'));
    }

    public function about()
    {
        $categories = Category::all();
        $products = Product::with('category')->latest()->take(8)->get();
        
        return view('frontend.about', compact('categories', 'products'));
    }

    public function appointment()
    {
        $categories = Category::all();
        $products = Product::with('category')->latest()->take(8)->get();
        
        return view('frontend.appointment', compact('categories', 'products'));
    }

    public function contact()
    {
        $categories = Category::all(); 
        
        return view('frontend.contact', compact('categories'));
    }

public function contactStore(Request $request)
{
    $validated = $request->validate([
        'name'    => 'required|string|max:255',
        'phone'   => 'required|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
        'kvkk'    => 'required',
    ]);

    ContactMessage::create([
        'name'    => $validated['name'],
        'phone'   => $validated['phone'],
        'subject' => $validated['subject'],
        'message' => $validated['message'],
    ]);

    return redirect()->back()->with('success', 'Mesajınız başarıyla iletildi.');
}

    public function show(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        return view('product.partials.detail', compact('product'));
    }

    public function getProductDetail($id)
    {
        $product = Product::findOrFail($id);
        
        return view('product.partials.detail', compact('product'))->render();
    }
}