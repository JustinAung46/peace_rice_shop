<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OptimizeProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize-product-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize existing product images to WebP and remove originals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::whereNotNull('image_path')->get();
        $this->info("Found {$products->count()} products with images.");
        
        $manager = new ImageManager(new Driver());
        $successCount = 0;
        $skipCount = 0;
        $failCount = 0;

        foreach ($products as $product) {
            $oldPath = $product->image_path;
            
            // Skip if it's already an optimized webp file we created in thumbnails directory
            if (str_contains($oldPath, 'thumbnails/') && str_ends_with(strtolower($oldPath), '.webp')) {
                $this->line("Skipping Product ID {$product->id}: Already optimized.");
                $skipCount++;
                continue;
            }

            if (!Storage::disk('public')->exists($oldPath)) {
                $this->error("Product ID {$product->id}: Original image not found at {$oldPath}.");
                $failCount++;
                continue;
            }

            try {
                $fileContent = Storage::disk('public')->get($oldPath);
                $img = $manager->read($fileContent);
                $img->scaleDown(width: 600, height: 600);
                
                $filename = uniqid('product_', true) . '.webp';
                $newPath = 'products/thumbnails/' . $filename;
                
                Storage::disk('public')->makeDirectory('products/thumbnails');
                Storage::disk('public')->put($newPath, (string) $img->toWebp(80));
                
                // Update database
                $product->update(['image_path' => $newPath]);
                
                // Now safely delete the old original
                Storage::disk('public')->delete($oldPath);
                
                // Also check and delete old thumbnail if it existed with the exact same base name
                $oldFilename = basename($oldPath);
                if (Storage::disk('public')->exists('products/thumbnails/' . $oldFilename)) {
                    Storage::disk('public')->delete('products/thumbnails/' . $oldFilename);
                }

                $this->info("Product ID {$product->id}: Optimized successfully.");
                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to optimize product image for ID {$product->id}: " . $e->getMessage());
                $this->error("Product ID {$product->id}: Failed - " . $e->getMessage());
                $failCount++;
            }
        }
        
        $this->info("Done! Optimized: $successCount, Skipped: $skipCount, Failed: $failCount.");
    }
}
