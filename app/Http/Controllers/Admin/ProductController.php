<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => 200,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|numeric',
            'sku' => 'required|unique:products,sku',
            'qty' => 'required',
            'is_featured' => 'required',
            'status' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $product = new Product;
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->category_id = $request->category;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->qty = $request->qty;
        $product->bar_code = $request->bar_code;
        $product->save();
        if ($request->has('gallery') && is_array($request->gallery)) {
            foreach ($request->gallery as $key => $tempImageId) {

                $tempImage = TempImage::find($tempImageId);

                if (! $tempImage) {
                    continue;
                }

                $path = public_path('uploads/temp/'.$tempImage->name);

                if (! file_exists($path)) {
                    continue;
                }

                try {
                    $ext = pathinfo($tempImage->name, PATHINFO_EXTENSION);
                    $rand = rand(100000, 10000000);
                    $imageName = $product->id.'-'.$rand.time().'.'.$ext;

                    $manager = new ImageManager(new Driver);

                    // LARGE
                    $image = $manager->read($path);
                    $image->scaleDown(400, 460);
                    $image->save(public_path('uploads/products/large/'.$imageName));

                    // SMALL
                    $image = $manager->read($path);
                    $image->coverDown(400, 460);
                    $image->save(public_path('uploads/products/small/'.$imageName));

                    $productImage = new ProductImage;
                    $productImage->image = $imageName;
                    $productImage->product_id = $product->id;
                    $productImage->save();

                    if (! empty($request->sizes)) {
                        ProductSize::where('product_id', $product->id)->delete();
                        foreach ($request->sizes as $sizeId) {
                            $productSize = new ProductSize;
                            $productSize->size_id = $sizeId;
                            $productSize->product_id = $product->id;
                            $productSize->save();
                        }
                    }

                    if (empty($product->image)) {
                        $product->image = $imageName;
                        $product->save();
                    }
                } catch (\Exception $e) {
                    \Log::error('Image processing failed: '.$e->getMessage());

                    continue;
                }
            }
        }

        return response()->json([
            'status' => 201,
            'message' => 'Product created successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['productImages', 'productSizes'])->find($id);
        if ($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'The product not found.',
            ]);
        }

        $productSizes = $product->productSizes()->pluck('size_id');

        return response()->json([
            'status' => 200,
            'data' => $product,
            'productSizes' => $productSizes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|numeric',
            'sku' => ['required', Rule::unique('products', 'sku')->ignore($id)],
            'is_featured' => 'required',
            'qty' => 'required',
            'status' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $product = Product::find($id);
        if ($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'The product not found.',
            ]);
        }
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->category_id = $request->category;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $request->status;
        $product->qty = $request->qty;
        $product->is_featured = $request->is_featured;
        $product->bar_code = $request->bar_code;
        $product->save();

        if (! empty($request->sizes)) {
            ProductSize::where('product_id', $product->id)->delete();
            foreach ($request->sizes as $sizeId) {
                $productSize = new ProductSize;
                $productSize->size_id = $sizeId;
                $productSize->product_id = $product->id;
                $productSize->save();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if ($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'The product not found.',
            ]);
        }

        $product->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully.',
        ]);
    }

    public function saveProductImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $image = $request->file('image');
        $rand = rand(100000, 10000000);
        $imageName = $request->product_id.'-'.$rand.time().'.'.$image->extension();
        $image->move(public_path('uploads/temp'), $imageName);
        $path = public_path('uploads/temp/'.$imageName);

        $manager = new ImageManager(new Driver);

        // LARGE
        $image = $manager->read($path);
        $image->scaleDown(400, 460);
        $image->save(public_path('uploads/products/large/'.$imageName));

        // SMALL
        $image = $manager->read($path);
        $image->coverDown(400, 460);
        $image->save(public_path('uploads/products/small/'.$imageName));

        $productImage = new ProductImage;
        $productImage->image = $imageName;
        $productImage->product_id = $request->product_id;
        $productImage->save();

        return response()->json([
            'status' => 201,
            'message' => 'Image has been uploaded successfully',
            'image' => $productImage,
        ]);
    }

    public function updateDefaultImage(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->image = $request->image;
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Product image changed successfully.',
        ], 200);
    }
}
