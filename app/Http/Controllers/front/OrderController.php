<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'state' => 'required',
            'mobile' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'grand_total' => 'required',
            'sub_total' => 'required',
            'discount' => 'required',
            'shipping' => 'required',
            'payment_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

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
                $orderItem->name = $val['title'];
                $orderItem->unit_price = $val['price'];
                $orderItem->qty = $val['qty'];
                $orderItem->product_id = $val['product_id'];
                $orderItem->size = $val['size'];
                $orderItem->price = $val['qty'] * $val['price'];
                $orderItem->save();
            }

            return response()->json([
                'status' => 201,
                'id' => $order->id,
                'message' => 'Order created successfully.',
            ], 201);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Cart is empty.',
            ], 400);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            if ($request->amount > 0) {
                Stripe::setApiKey(env('STRIPE_PRIVATE_KEY'));
                $paymentIntent = PaymentIntent::create([
                    'amount' => $request->amount,
                    'currency' => 'USD',
                    'payment_method_types' => ['card'],
                ]);

                $clientSecret = $paymentIntent->client_secret;

                return response()->json([
                    'status' => 200,
                    'token' => $clientSecret,
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Amount must be greater than 0',
                ], 400);
            }
        } catch (Exception $e) {
        }
    }
}
