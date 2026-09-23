<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->with('product')->get();
        
        return view('dashboard.cart', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;
        $variantId = $request->variant_id; 
        $quantity = $request->quantity;

        $cartItem = Cart::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->where('variant_id', $variantId)
                        ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity
            ]);
        }

        return redirect('/dashboard/sepetim')->with('success', 'Ürün başarıyla sepete eklendi!');
    }

    public function remove($id)
    {
        $cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cartItem->delete();

        return back()->with('success', 'Ürün sepetten çıkarıldı.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        $cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Sepet güncellendi.');
    }

    public function checkout()
    {
        $cart = Cart::where('user_id', Auth::id())->with('product')->get();
        
        $grandTotal = $cart->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        $shippingTotal = $cart->sum(function($item) {
            return $item->product->shipping_price ?? 0;
        });

        return view('dashboard.checkout', compact('cart', 'grandTotal', 'shippingTotal'));
    }

public function storeOrder(Request $request)
{
    $request->validate([
        'address' => 'required|min:10',
        'agreement' => 'accepted',
        'kvkk' => 'accepted',
    ]);

    $paymentSuccess = true; 

    if ($paymentSuccess) {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        $subTotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
        
        $shippingTotal = $cartItems->sum(fn($i) => $i->product->shipping_price ?? 0);
        
        $totalWithShipping = $subTotal + $shippingTotal;

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(4))),
            'address' => $request->address,
            'total_price' => $totalWithShipping, 
            'status' => 'paid', 
            'transaction_id' => 'TXN-' . time()
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();

        return redirect(url('dashboard/hesabim/siparislerim'))->with('success', 'Siparişiniz Öncel Perde tarafından alınmıştır. En kısa sürede hazırlanıp teslim edilecektir, güzel günlerde kullanın!');
    }

    return back()->with('error', 'Ödeme başarısız, lütfen bilgileri kontrol edin.');
}

    public function moveToWishlist($id) 
    {
        $cartItem = \App\Models\Cart::findOrFail($id);
        
        \App\Models\Wishlist::updateOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $cartItem->product_id
        ]);
        
        $cartItem->delete();
        
        return back()->with('info', 'Ürün favorilerinize taşındı.');
    }
}