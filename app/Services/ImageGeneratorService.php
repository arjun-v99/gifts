<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\PngEncoder;

class ImageGeneratorService
{
    /**
     * Creates a simple text-based sticker image and saves it to storage.
     *
     * @param string $text The text to put on the sticker.
     * @param int $width The width of the image.
     * @param int $height The height of the image.
     * @param string $folder The folder within the storage disk to save the image.
     * @return array An array containing 'status' (bool) and 'path' (string or null).
     */
    public function createSticker(
        string $text,
        int $width = 200,
        int $height = 100,
        string $folder = 'stickers'
    ): array {
        try {
            // 1. Initialize the Image Manager
            $manager = new ImageManager(new Driver());

            // 2. Define colors
            $backgroundColor = '#2c3e50'; // Dark Blue/Gray
            $textColor = '#ecf0f1';      // Light Gray/White

            // 3. Create the image instance
            $image = $manager->create($width, $height, $backgroundColor);

            // 4. Add text
            $image->text($text, $width / 2, $height / 2, function ($font) use ($textColor) {
                $font->size(20);
                $font->color($textColor);
                $font->align('center');
                $font->valign('middle');
            });

            // 5. Prepare to Save the File
            $filename = Str::uuid() . '.png';
            $path = $folder . '/' . $filename;
            $disk = 'private';

            // 6. Encode and Store the image
            $encodedImage = $image->encode(new PngEncoder());
            Storage::disk($disk)->put($path, $encodedImage);

            // Return success status and the file path
            return [
                'status' => true,
                'path' => $path,
            ];
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Image creation failed: " . $e->getMessage());

            // Return failure status
            return [
                'status' => false,
                'path' => null,
            ];
        }
    }
}
