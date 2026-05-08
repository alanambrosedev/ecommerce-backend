<?php

namespace App\Http\Controllers;

use App\Models\Size;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::orderBy('created_at', 'ASC')->get();

        return response()->json([
            'status' => 200,
            'data' => $sizes,
        ]);
    }
}
