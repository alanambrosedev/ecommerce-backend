<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::find(Auth::user()->id);

            if ($user->role == 'admin') {
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "UnAuthorised access."
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials.'
            ]);
        }
    }
}
