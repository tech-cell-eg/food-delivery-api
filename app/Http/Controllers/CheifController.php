<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use App\Models\Cheif;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;




class CheifController extends Controller
{
    //
    public function statistics($id)
    {
        $cheif = Cheif::find($id);
    
        if (!$cheif) {
            return response()->json([
                'message' => 'Cheif not found'
            ], 404);
        }
    
        $mealIds = Meal::where('cheif_id', $id)->pluck('id');
    
        $orderIds = DB::table('order_meal')
            ->whereIn('meal_id', $mealIds)
            ->pluck('order_id')
            ->unique();
    
        $totalOrders = $orderIds->count();
  
        $recentOrders = Order::whereIn('id', $orderIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    
        $totalRevenue = DB::table('order_meal')
            ->whereIn('meal_id', $mealIds)
            ->sum(DB::raw('price * quantity'));
    
        $averageRating = Rating::where('cheif_id', $id)->avg('rating') ?? 0;
    
        return response()->json([
            'total_orders'   => $totalOrders,
            'recent_orders'  => $recentOrders,
            'total_revenue'  => round($totalRevenue, 2),
            'average_rating' => round($averageRating, 2),
        ]);
    }
    
}
