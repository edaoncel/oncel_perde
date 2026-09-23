<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $todaySalesCount = OrderItem::whereDate('created_at', today())->sum('quantity');
        $todayRevenue = Order::whereDate('created_at', today())->sum('total_price');

        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_amount'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $monthlySales = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_price) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlySales[$i] ?? 0;
        }

        $latestOrders = Order::with(['user', 'items.product'])->latest()->paginate(15);

        return view('admin.orders.index', compact(
            'todaySalesCount',
            'todayRevenue',
            'topProducts',
            'chartData',
            'latestOrders'
        ));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

public function updateStatus(Request $request, $id)
{
    $order = \App\Models\Order::findOrFail($id);
    
    $request->validate([
        'status' => 'required|in:pending,preparing,shipped,delivered,cancelled',
    ]);

    $order->status = $request->status;
    
    $order->save();

    return redirect()->back()->with('success', 'Sipariş durumu başarıyla güncellendi.');
}
}