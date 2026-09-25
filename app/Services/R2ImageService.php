<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Format;
use Intervention\Image\Interfaces\ImageManagerInterface;

class R2ImageService
{
    public function __construct(
        private ImageManagerInterface $imageManager,
    ) {
    }

    /**
     * Convert an uploaded image to WebP and upload it to R2.
     */
    public function uploadProductImage(UploadedFile $file): array
    {
        $image = $this->imageManager->decode($file);

        $encodedImage = $image->encodeUsingFormat(
            Format::WEBP,
            quality: 85,
        );

        $path = 'products/' . Str::uuid() . '.webp';

        Storage::disk('r2')->put(
            $path,
            (string) $encodedImage,
            [
                'ContentType' => 'image/webp',
                'CacheControl' => 'public, max-age=31536000',
            ],
        );

        return [
            'path' => $path,
            'url' => Storage::disk('r2')->url($path),
        ];
    }

    /**
     * Delete an image from R2.
     */
    public function delete(string $path): void
    {
        Storage::disk('r2')->delete($path);
    }
}
