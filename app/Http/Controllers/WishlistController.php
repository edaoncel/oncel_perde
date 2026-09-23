<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->with('product')->get();
        return view('dashboard.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Giriş yapmalısınız.'], 401);
        }

        $productId = $request->product_id;
        $color = $request->color; 
        $userId = Auth::id();

        $wishlist = Wishlist::where('user_id', $userId)
                            ->where('product_id', $productId)
                            ->where('color', $color)
                            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'removed', 'message' => 'Favorilerden çıkarıldı.']);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'color' => $color
            ]);
            return response()->json(['status' => 'added', 'message' => 'Favorilere eklendi.']);
        }
    }

    public function remove($id)
    {
        $wishlist = Wishlist::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $wishlist->delete();

        return back()->with('success', 'Favorilerden silindi.');
    }

    public function checkStatus(Request $request, $productId)
    {
        if (!Auth::check()) {
            return response()->json(['is_favorited' => false]);
        }

        $color = $request->query('color');

        $isFavorited = Wishlist::where('user_id', Auth::id())
                                ->where('product_id', $productId)
                                ->where('color', $color)
                                ->exists();

        return response()->json(['is_favorited' => $isFavorited]);
    }
}