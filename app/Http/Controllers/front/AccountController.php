<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(AccountRequest $request)
    {
        $user = User::create([...$request->validated(), 'role' => 'customer']);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], Response::HTTP_CREATED);
    }

    public function authenticate(LoginRequest $request)
    {

        $result = $this->authService->authenticate($request->only('email', 'password'), 'customer', 'customer-auth-token');

        return response()->json($result['data'], $result['status']);
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
                'status' => 422,
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
