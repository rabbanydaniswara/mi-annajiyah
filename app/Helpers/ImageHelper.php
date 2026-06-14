<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class ImageHelper
{
    private const TRANSPARENT_PIXEL = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

    /**
     * Upload and optimize image
     *
     * @param  string  $directory  Directory inside public_path
     * @param  string|null  $prefix  Filename prefix
     * @param  int  $quality  Compression quality (0-100)
     * @param  int|null  $maxWidth  Max width for resizing
     * @param  bool  $generateThumbnail  Whether to generate a 200x200 thumbnail
     * @return string Relative path of the saved file
     */
    public static function uploadAndOptimize(UploadedFile $file, string $directory = 'uploads', ?string $prefix = null, int $quality = 70, int $maxWidth = 1600, bool $generateThumbnail = true): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'bmp']);

        $filename = ($prefix ? $prefix.'_' : '').time().'_'.Str::random(5);
        $fullDirectory = public_path($directory);

        if (! file_exists($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }

        // If not an image (e.g. PDF), just move it
        if (! $isImage) {
            $finalName = $filename.'.'.$extension;
            $file->move($fullDirectory, $finalName);

            return $directory.'/'.$finalName;
        }

        // Process Image
        $finalName = $filename.'.webp';
        $destinationPath = $fullDirectory.'/'.$finalName;

        // Load image using GD
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($file->getRealPath());
                break;
            case 'png':
                $img = imagecreatefrompng($file->getRealPath());
                // Handle transparency for PNG
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
                break;
            case 'webp':
                $img = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                // Fallback for other types
                $file->move($fullDirectory, $filename.'.'.$extension);

                return $directory.'/'.$filename.'.'.$extension;
        }

        if (! $img) {
            // Fallback if GD fails to load
            $file->move($fullDirectory, $filename.'.'.$extension);

            return $directory.'/'.$filename.'.'.$extension;
        }

        // Resize if needed
        $width = imagesx($img);
        $height = imagesy($img);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $newWidth);
            $tmp = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for resize
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);

            imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($img);
            $img = $tmp;

            // Update width and height for thumbnail generation
            $width = $newWidth;
            $height = $newHeight;
        }

        // Save as WebP (Main)
        if (! imagewebp($img, $destinationPath, $quality)) {
            imagedestroy($img);
            throw new RuntimeException('Gambar gagal dioptimalkan.');
        }

        // Generate and Save Thumbnail
        if ($generateThumbnail) {
            $thumbSize = 200;
            $thumbName = $filename.'_thumb.webp';
            $thumbPath = $fullDirectory.'/'.$thumbName;

            $thumbImg = imagecreatetruecolor($thumbSize, $thumbSize);
            imagealphablending($thumbImg, false);
            imagesavealpha($thumbImg, true);

            $minDim = min($width, $height);
            $cropX = ($width - $minDim) / 2;

            // Logika crop untuk gambar portrait (berdiri):
            // Wajah biasanya berada di bagian atas. Daripada memotong tepat di tengah (50%),
            // kita potong 15% dari atas agar wajah/kepala tidak terpotong.
            if ($height > $width) {
                $cropY = ($height - $minDim) * 0.15;
            } else {
                $cropY = ($height - $minDim) / 2;
            }

            imagecopyresampled($thumbImg, $img, 0, 0, (int) $cropX, (int) $cropY, $thumbSize, $thumbSize, $minDim, $minDim);
            imagewebp($thumbImg, $thumbPath, 60); // lower quality for thumbs
            imagedestroy($thumbImg);
        }

        imagedestroy($img);

        return $directory.'/'.$finalName;
    }

    /**
     * Generate a 200x200 _thumb.webp for an existing public-relative image path.
     * Returns true on success, false if source missing / unsupported / GD failed.
     * Idempotent: skips if thumb already exists.
     */
    public static function generateThumbnailFor(string $relativePath, bool $force = false): bool
    {
        $relativePath = ltrim($relativePath, '/');
        $src = public_path($relativePath);
        if (! file_exists($src)) {
            return false;
        }

        $info = pathinfo($relativePath);
        $thumbRel = ($info['dirname'] === '.' ? '' : $info['dirname'].'/').$info['filename'].'_thumb.webp';
        $thumbAbs = public_path($thumbRel);
        if (! $force && file_exists($thumbAbs)) {
            return true;
        }

        $ext = strtolower($info['extension'] ?? '');
        switch ($ext) {
            case 'jpg': case 'jpeg': $img = @imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = @imagecreatefrompng($src);
                if ($img) {
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                }
                break;
            case 'webp': $img = @imagecreatefromwebp($src);
                break;
            default: return false;
        }
        if (! $img) {
            return false;
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $size = 200;
        $thumb = imagecreatetruecolor($size, $size);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $min = min($w, $h);

        $cropX = ($w - $min) / 2;
        if ($h > $w) {
            $cropY = ($h - $min) * 0.15;
        } else {
            $cropY = ($h - $min) / 2;
        }

        imagecopyresampled($thumb, $img, 0, 0, (int) $cropX, (int) $cropY, $size, $size, $min, $min);

        if (! is_dir(dirname($thumbAbs))) {
            @mkdir(dirname($thumbAbs), 0755, true);
        }
        $ok = imagewebp($thumb, $thumbAbs, 60);
        imagedestroy($img);
        imagedestroy($thumb);

        return (bool) $ok;
    }

    /**
     * Get thumbnail path for a given image path.
     * Falls back to the original file if the *_thumb.webp variant does not exist
     * (e.g. for seeded / manually-uploaded files that bypassed uploadAndOptimize).
     */
    public static function getThumbnail(string $path): string
    {
        if (! $path) {
            return '';
        }
        $path = ltrim($path, '/');
        $info = pathinfo($path);
        $thumbRel = ($info['dirname'] === '.' ? '' : $info['dirname'].'/').$info['filename'].'_thumb.webp';
        if (file_exists(public_path($thumbRel))) {
            return $thumbRel;
        }

        return $path;
    }

    /**
     * Delete a thumbnail counterpart if it exists.
     */
    public static function deleteThumbnail(string $path): void
    {
        if (! $path) {
            return;
        }
        $path = ltrim($path, '/');
        self::deletePublicUploadFile(self::variantPath($path, 'thumb'));
    }

    /**
     * Delete an uploaded image and generated variants safely.
     */
    public static function deleteImageSet(?string $path, array $suffixes = ['thumb', 'card', 'hero']): void
    {
        if (! $path) {
            return;
        }

        $path = ltrim($path, '/');
        $targets = [$path];

        foreach ($suffixes as $suffix) {
            $targets[] = self::variantPath($path, $suffix);
        }

        foreach (array_unique($targets) as $target) {
            self::deletePublicUploadFile($target);
        }
    }

    /**
     * Generate a resized WebP variant for an existing public-relative image.
     * The original file is kept untouched so database references remain stable.
     */
    public static function generateVariantFor(string $relativePath, string $suffix, int $maxWidth, int $quality = 68, bool $force = false): bool
    {
        $relativePath = ltrim($relativePath, '/');
        $src = public_path($relativePath);
        if (! file_exists($src)) {
            return false;
        }

        $variantRel = self::variantPath($relativePath, $suffix);
        $variantAbs = public_path($variantRel);
        if (! $force && file_exists($variantAbs)) {
            return true;
        }

        $img = self::loadImageResource($src, pathinfo($relativePath, PATHINFO_EXTENSION));
        if (! $img) {
            return false;
        }

        $width = imagesx($img);
        $height = imagesy($img);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($height / $width) * $newWidth);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($img);
            $img = $resized;
        }

        if (! is_dir(dirname($variantAbs))) {
            @mkdir(dirname($variantAbs), 0755, true);
        }
        $ok = imagewebp($img, $variantAbs, $quality);
        imagedestroy($img);

        return (bool) $ok;
    }

    /**
     * Get a card-sized image variant, falling back safely to existing assets.
     */
    public static function getCard(string $path): string
    {
        return self::getVariant($path, 'card') ?: self::getThumbnail($path);
    }

    /**
     * Get a hero-sized image variant, falling back safely to the WebP original.
     */
    public static function getHero(string $path): string
    {
        return self::getVariant($path, 'hero') ?: self::getWebp($path);
    }

    public static function transparentPixel(): string
    {
        return self::TRANSPARENT_PIXEL;
    }

    private static function getVariant(string $path, string $suffix): ?string
    {
        if (! $path) {
            return null;
        }

        $path = ltrim($path, '/');
        $variantRel = self::variantPath($path, $suffix);

        return file_exists(public_path($variantRel)) ? $variantRel : null;
    }

    private static function variantPath(string $path, string $suffix): string
    {
        $info = pathinfo(ltrim($path, '/'));

        return ($info['dirname'] === '.' ? '' : $info['dirname'].'/').$info['filename'].'_'.$suffix.'.webp';
    }

    private static function deletePublicUploadFile(string $relativePath): void
    {
        $relativePath = str_replace('\\', '/', ltrim($relativePath, '/'));
        if (! $relativePath || str_contains($relativePath, '..')) {
            return;
        }

        $uploadsRoot = realpath(public_path('uploads'));
        if (! $uploadsRoot) {
            return;
        }

        $absolutePath = public_path($relativePath);
        $directory = realpath(dirname($absolutePath));
        if (! $directory) {
            return;
        }

        $root = rtrim(strtolower(str_replace('\\', '/', $uploadsRoot)), '/');
        $dir = rtrim(strtolower(str_replace('\\', '/', $directory)), '/');
        if ($dir !== $root && ! str_starts_with($dir, $root.'/')) {
            return;
        }

        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private static function loadImageResource(string $src, ?string $extension)
    {
        switch (strtolower((string) $extension)) {
            case 'jpg':
            case 'jpeg':
                return @imagecreatefromjpeg($src);
            case 'png':
                $img = @imagecreatefrompng($src);
                if ($img) {
                    imagepalettetotruecolor($img);
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                }

                return $img;
            case 'webp':
                return @imagecreatefromwebp($src);
            default:
                return false;
        }
    }

    /**
     * Get WebP variant if exists, otherwise return original.
     */
    public static function getWebp(string $path): string
    {
        if (! $path) {
            return '';
        }
        $path = ltrim($path, '/');
        $info = pathinfo($path);

        // Skip if already webp
        if (strtolower($info['extension'] ?? '') === 'webp') {
            return $path;
        }

        $webpRel = ($info['dirname'] === '.' ? '' : $info['dirname'].'/').$info['filename'].'.webp';
        if (file_exists(public_path($webpRel))) {
            return $webpRel;
        }

        return $path;
    }
}
