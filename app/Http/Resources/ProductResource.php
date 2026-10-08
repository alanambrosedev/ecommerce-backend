<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => (float) $this->price,
            'compare_price' => $this->compare_price !== null ? (float) $this->compare_price : null,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'image_url' => $this->image_url,
            'sku' => $this->sku,
            'bar_code' => $this->bar_code,
            'qty' => (int) $this->qty,
            'status' => (int) $this->status,
            'is_featured' => (bool) $this->is_featured,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ]),
            'images' => $this->whenLoaded(
                'productImages',
                fn () => $this->productImages->map(fn ($img) => [
                    'id' => $img->id,
                    'image_url' => $img->image_url ?? asset('uploads/products/small/'.$img->image),
                ])
            ),
            'sizes' => $this->whenLoaded(
                'productSizes',
                fn () => $this->productSizes->map(fn ($item) => [
                    'id' => $item->id,
                    'size_id' => $item->size_id,
                    'name' => $item->size?->name,
                ])
            ),

        ];
    }
}
