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

        // If the path already points to the optimized thumbnail, return it directly
        // to avoid unnecessary disk checks.
        if (str_contains($this->image_path, 'thumbnails/')) {
            return asset('storage/' . $this->image_path);
        }

        // Fallback for old images that haven't been migrated yet.
        // Try to find a thumbnail in the "thumbnails" subfolder.
        $pathParts = explode('/', $this->image_path);
        
        if (count($pathParts) >= 2) {
            $filename = array_pop($pathParts);
            $dir = implode('/', $pathParts);
            $thumbnailPath = $dir . '/thumbnails/' . $filename;
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                return asset('storage/' . $thumbnailPath);
            }
        }
        
        // Final fallback to the original image
        return asset('storage/' . $this->image_path);
    }

    public function scopeWithActiveCategory($query)
    {
        // Use whereIn with a subquery instead of whereHas to avoid correlated EXISTS subquery
        return $query->whereIn('category_id', function ($q) {
            $q->select('id')->from('categories')->where('is_active', true);
        });
    }
}
