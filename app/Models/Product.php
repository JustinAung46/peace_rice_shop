<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'sku', 'image_path', 'current_selling_price', 'price_per_pyi', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

}
