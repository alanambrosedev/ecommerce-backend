<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {
        if (! empty($request->cart)) {
            $order = new Order;
            $order->name = $request->name;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->state = $request->state;
            $order->mobile = $request->mobile;
            $order->zip = $request->zip;
            $order->city = $request->city;
            $order->grand_total = $request->grand_total;
            $order->sub_total = $request->sub_total;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->payment_status = $request->payment_status;
            $order->status = $request->status;
            $order->user_id = auth()->id();
            $order->save();

            foreach ($request->cart as $key => $val) {

                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->name = $val['name'];
                $orderItem->unit_price = $val['price'];
                $orderItem->qty = $val['qty'];
                $orderItem->product_id = $val['product_id'];
                $orderItem->size = $val['size'];
                $orderItem->price = $val['qty'] * $val['price'];
                $orderItem->save();
            }

            return response()->json([
                'status' => 201,
                'message' => 'Order created successfully.',
            ], 201);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Cart is empty.',
            ], 400);
        }
    }
}
