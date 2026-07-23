<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => 200,
            'data' => $orders,

        ], 200);
    }

    public function show($id)
    {
        $order = Order::with('items')->find($id);

        if ($order == null) {
            return response()->json([
                'data' => [],
                'message' => "order not found.",
                'status' => 404
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $order,

        ], 200);
    }
}
