<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'customer';
        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'User created successfully.',
        ], 201);
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::find(Auth::user()->id);

            if ($user->role !== 'customer') {
                Auth::logout();

                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized access.',
                ]);
            }

            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'token' => $token,
                'id' => $user->id,
                'name' => $user->name,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials.',
            ]);
        }
    }

    public function getOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => 200,
            'data' => $orders,
        ]);
    }

    public function getOrderDetails($id, Request $request)
    {
        $order = Order::with('items', 'items.product')->where([
            'user_id' => $request->user()->id,
            'id' => $id,
        ])->first();

        if ($order == null) {
            return response()->json([
                'data' => [],
                'message' => 'Order not found.',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $order,

        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find($request->user()->id);

        if ($user == null) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found.',
                'data' => [],
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email, '.$request->user()->id.',id',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'zip' => 'required|max:100',
            'mobile' => 'required|max:100',
            'address' => 'required|max:100',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        $user->name = $request->name;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->email = $request->email;
        $user->zip = $request->zip;
        $user->mobile = $request->mobile;
        $user->address = $request->address;

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User updated successfully.',
            'data' => $user,
        ], 200);
    }

    public function getAccountDetails(Request $request)
    {
        $user = User::find($request->user()->id);

        if ($user == null) {
            return response()->json([
                'status' => 404,
                'message' => 'User not found.',
                'data' => [],
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'data' => $user,
            ], 200);
        }
    }
}
