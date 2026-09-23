<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Appointment;
use App\Models\ContactMessage;
use App\Models\Order; 
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $pendingAppointments = Appointment::where('status', 'Bekliyor')->count();
        
        $recentAppointments = Appointment::where('status', 'Bekliyor')
                                         ->latest()
                                         ->take(4)
                                         ->get();

        $pendingMessages = ContactMessage::where('is_read', 0)->count(); 
        $recentMessages = ContactMessage::where('is_read', 0)->latest()->take(4)->get();

        $categoryChartData = Category::withCount('products')->get();

        $chartData = [];
        for ($month = 1; $month <= 12; $month++) {
            $chartData[] = Order::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $month)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price');
        }

        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalCategories', 
            'pendingAppointments', 
            'recentAppointments', 
            'categoryChartData',
            'pendingMessages', 
            'recentMessages',
            'chartData'
        ));
    }
}