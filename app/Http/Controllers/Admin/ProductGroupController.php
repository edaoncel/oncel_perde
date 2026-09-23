<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function index() 
    {
        $groups = ProductGroup::all();
        return view('admin.product-groups.index', compact('groups'));
    }

    public function store(Request $request) 
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        ProductGroup::create($request->all());
        
        return back()->with('success', 'Grup başarıyla oluşturuldu.');
    }

    public function destroy($id)
    {
        $group = \App\Models\ProductGroup::findOrFail($id);
        $group->delete();
        return back()->with('success', 'Grup başarıyla silindi.');
    }
}