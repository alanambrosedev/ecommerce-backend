<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getLatestProducts()
    {
        $latestProducts = Product::orderBy('created_at', 'DESC')->where('status', 1)->limit(8)->get();

        return response()->json([
            'status' => 200,
            'data' => $latestProducts,
        ]);
    }

    public function getFeaturedProducts()
    {
        $featuredProducts = Product::orderBy('created_at', 'DESC')->where('status', 1)->where('is_featured', 'yes')->limit(8)->get();

        return response()->json([
            'status' => 200,
            'data' => $featuredProducts,
        ]);
    }

    public function getBrands()
    {
        $brands = Brand::orderBy('created_at', 'ASC')->where('status', 1)->get();

        return response()->json([
            'status' => 200,
            'data' => $brands,
        ]);
    }

    public function getCategories()
    {
        $categories = Category::orderBy('created_at', 'ASC')->where('status', 1)->get();

        return response()->json([
            'status' => 200,
            'data' => $categories,
        ]);
    }

    public function getProducts(Request $request)
    {
        $query = Product::orderBy('created_at', 'ASC')->where('status', 1);

        if ($request->category) {
            $query->whereIn('category_id', explode(',', $request->category));
        }
        if ($request->brand) {
            $query->whereIn('brand_id', explode(',', $request->brand));
        }

        $products = $query->get();

        return response([
            'status' => 200,
            'data' => $products,
        ]);
    }

    public function getProductDetails($id)
    {
        $productDetails = Product::with('productImages', 'productSizes.size')->find($id);

        if ($productDetails == null) {
            return response()->json([
                'status' => 404,
                'message' => "Product not found."
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $productDetails
        ]);
    }
}
