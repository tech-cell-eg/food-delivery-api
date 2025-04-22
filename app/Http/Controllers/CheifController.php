<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use App\Models\Cheif;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponse ;




class CheifController extends Controller
{
    //
    use ApiResponse;
    public function statistics($id)
    {
        $cheif = Cheif::find($id);
    
        if (!$cheif) {
            return $this->errorResponse('Cheif not found', 404);
        }
    
        $mealIds = Meal::where('cheif_id', $id)->pluck('id');
        $mealsCount = $cheif->meals->count(); 
    $mealNames = $cheif->meals->pluck('name'); 
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
        return $this->successResponse([
          'cheif_id'       => $id,
          'cheif_name'     => $cheif->name,
          'total_orders'   => $totalOrders,
          'recent_orders'  => $recentOrders,
          'total_revenue'  => round($totalRevenue, 2),
          'average_rating' => round($averageRating, 2),
          'meals_count'    => $mealsCount,
          'meal_names'     => $mealNames,
      ], 'Cheif statistics retrieved successfully', 200);
      
    }
    public function getCheifOrders($id)
    {
        $cheif = Cheif::find($id);
    
        if (!$cheif) {
            return $this->errorResponse('Cheif not found', 404);
          }
    
        $orders = Order::whereHas('meals', function ($query) use ($id) {
            $query->where('cheif_id', $id);
        })->with(['user', 'restaurant', 'address', 'offer', 'meals'])->get();
    return $this->successResponse($orders, 'Cheif orders retrieved successfully', 200);
    }
    
}
