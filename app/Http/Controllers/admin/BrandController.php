<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200,
            'data' => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->status =  1;
        $brand->save();

        return response()->json([
            'status' => 200,
            'message' => 'Brand added successfully.',
            'data' => $brand
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);

        if ($brand == null) {
            return response()->json([
                'status' =>  404,
                'message' => "Brand not found.",
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $brand
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }
        $brand = Brand::find($id);

        if ($brand == null) {
            return response()->json([
                'status' =>  404,
                'message' => "Brand not found.",
                'data' => []
            ], 404);
        }

        $brand->name = $request->name;
        $brand->status =  1;
        $brand->save();

        return response()->json([
            'status' => 200,
            'message' => 'Brand updated successfully.',
            'data' => $brand
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);

        if ($brand == null) {
            return response()->json([
                'status' =>  404,
                'message' => "Brand not found.",
                'data' => []
            ], 404);
        }

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => "Brand deleted successfully."
        ]);
    }
}
