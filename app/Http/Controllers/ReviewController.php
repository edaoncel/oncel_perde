<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;

class ReviewController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'rating'     => 'required|integer|min:1|max:5',
        'comment'    => 'required|string|max:1000',
        'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $data = [
        'user_id'    => auth()->id(),
        'product_id' => $request->product_id,
        'rating'     => $request->rating,
        'comment'    => $request->comment,
    ];

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('reviews', 'public');
        $data['image'] = $imagePath;
    }

    Review::create($data);

    return back()->with('success', 'Yorumunuz başarıyla eklendi!');
}
}