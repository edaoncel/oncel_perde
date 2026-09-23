<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function success($id)
    {
        $order = Order::findOrFail($id);
        return view('dashboard.order-success', compact('order'));
    }

public function myOrders()
{
    $userId = \Illuminate\Support\Facades\Auth::id();

    $orders = \App\Models\Order::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();


    return view('dashboard.orders', compact('orders'));
}

public function storeOrder(Request $request)
    {
        $request->validate([
            'address' => 'required',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Sepetiniz boş.');
        }

        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . strtoupper(bin2hex(random_bytes(4))),
            'address' => $request->address,
            'total_price' => $total,
            'status' => 'paid',
            'transaction_id' => 'TXN-' . time(),
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();

        return redirect()->to('dashboard/hesabim/siparislerim')->with('success', 'Siparişiniz başarıyla alındı!');
    }
}