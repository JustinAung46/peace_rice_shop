<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'image_path', 'category_id', 'is_active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function getThumbnailUrlAttribute()
    {
        if (!$this->image_path) {
            return '';
        }

        // e.g. "products/my_image.jpg" -> "products/thumbnails/my_image.jpg"
        $pathParts = explode('/', $this->image_path);
        
        if (count($pathParts) >= 2) {
            // The directory is usually the first part, like "products"
            $filename = array_pop($pathParts);
            $dir = implode('/', $pathParts);
            $thumbnailPath = $dir . '/thumbnails/' . $filename;
            
            // Check if thumbnail exists
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                return asset('storage/' . $thumbnailPath);
            }
        }
        
        // Fallback to original image
        return asset('storage/' . $this->image_path);
    }
}
