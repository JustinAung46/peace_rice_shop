<?php

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('uploaded logo is resized and converted to WebP', function () {
    // Arrange: Create admin user and fake public storage
    Storage::fake('public');
    
    $admin = User::create([
        'name' => 'Admin User',
        'account_id' => 12345,
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
    
    // Create a large 600x600 PNG image
    $file = UploadedFile::fake()->image('large_logo.png', 600, 600);
    
    // Act: Send settings update post request
    $response = $this->actingAs($admin)
        ->post(route('accounts.settings.update'), [
            'site_logo' => $file,
            'shop_name' => 'Super Rice Shop',
        ]);
        
    // Assert: Verify request redirected back successfully
    $response->assertStatus(302);
    
    // Verify settings DB record is updated and has .webp extension
    $siteLogo = Setting::get('site_logo');
    expect($siteLogo)->not->toBeNull();
    expect($siteLogo)->toEndWith('.webp');
    
    // Verify file is saved on the fake public storage
    Storage::disk('public')->assertExists($siteLogo);
    
    // Get actual dimensions of the saved image
    $fullPath = Storage::disk('public')->path($siteLogo);
    list($width, $height) = getimagesize($fullPath);
    
    // Assert that the image was scaled down to fit within 300x300 bounding box
    expect($width)->toBeLessThanOrEqual(300);
    expect($height)->toBeLessThanOrEqual(300);
});
