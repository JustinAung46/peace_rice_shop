<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate thumbnails for all existing product images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = \App\Models\Product::whereNotNull('image_path')->get();
        $this->info("Found {$products->count()} products with images.");

        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('products/thumbnails');

        $bar = $this->output->createProgressBar(count($products));

        foreach ($products as $product) {
            $imagePath = $product->image_path;
            $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($imagePath);

            if (file_exists($fullPath)) {
                $filename = basename($imagePath);
                $pathParts = explode('/', $imagePath);
                $dir = implode('/', array_slice($pathParts, 0, -1));
                
                $thumbnailDir = $dir . '/thumbnails';
                $thumbnailPath = $thumbnailDir . '/' . $filename;

                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath)) {
                    try {
                        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($thumbnailDir);
                        
                        $img = $manager->read($fullPath);
                        $img->cover(300, 300);
                        
                        \Illuminate\Support\Facades\Storage::disk('public')->put($thumbnailPath, (string) $img->toJpeg(80));
                    } catch (\Exception $e) {
                        $this->error("\nFailed for product ID {$product->id}: {$e->getMessage()}");
                    }
                }
            } else {
                $this->warn("\nFile not found for product ID {$product->id}: {$fullPath}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nThumbnails generated successfully!");
    }
}
