<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Upload and optimize image
     * 
     * @param UploadedFile $file
     * @param string $directory Directory inside public_path
     * @param string|null $prefix Filename prefix
     * @param int $quality Compression quality (0-100)
     * @param int|null $maxWidth Max width for resizing
     * @param bool $generateThumbnail Whether to generate a 200x200 thumbnail
     * @return string Relative path of the saved file
     */
    public static function uploadAndOptimize(UploadedFile $file, string $directory = 'uploads', string $prefix = null, int $quality = 70, int $maxWidth = 1600, bool $generateThumbnail = true): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'bmp']);
        
        $filename = ($prefix ? $prefix . '_' : '') . time() . '_' . Str::random(5);
        $fullDirectory = public_path($directory);
        
        if (!file_exists($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }

        // If not an image (e.g. PDF), just move it
        if (!$isImage) {
            $finalName = $filename . '.' . $extension;
            $file->move($fullDirectory, $finalName);
            return $directory . '/' . $finalName;
        }

        // Process Image
        $finalName = $filename . '.webp';
        $destinationPath = $fullDirectory . '/' . $finalName;

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
                $file->move($fullDirectory, $filename . '.' . $extension);
                return $directory . '/' . $filename . '.' . $extension;
        }

        if (!$img) {
            // Fallback if GD fails to load
            $file->move($fullDirectory, $filename . '.' . $extension);
            return $directory . '/' . $filename . '.' . $extension;
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
        imagewebp($img, $destinationPath, $quality);

        // Generate and Save Thumbnail
        if ($generateThumbnail) {
            $thumbSize = 200;
            $thumbName = $filename . '_thumb.webp';
            $thumbPath = $fullDirectory . '/' . $thumbName;
            
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
            
            imagecopyresampled($thumbImg, $img, 0, 0, (int)$cropX, (int)$cropY, $thumbSize, $thumbSize, $minDim, $minDim);
            imagewebp($thumbImg, $thumbPath, 60); // lower quality for thumbs
            imagedestroy($thumbImg);
        }
        
        imagedestroy($img);

        return $directory . '/' . $finalName;
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
        if (!file_exists($src)) return false;

        $info = pathinfo($relativePath);
        $thumbRel = ($info['dirname'] === '.' ? '' : $info['dirname'] . '/') . $info['filename'] . '_thumb.webp';
        $thumbAbs = public_path($thumbRel);
        if (!$force && file_exists($thumbAbs)) return true;

        $ext = strtolower($info['extension'] ?? '');
        switch ($ext) {
            case 'jpg': case 'jpeg': $img = @imagecreatefromjpeg($src); break;
            case 'png':
                $img = @imagecreatefrompng($src);
                if ($img) { imagepalettetotruecolor($img); imagealphablending($img, true); imagesavealpha($img, true); }
                break;
            case 'webp': $img = @imagecreatefromwebp($src); break;
            default: return false;
        }
        if (!$img) return false;

        $w = imagesx($img); $h = imagesy($img);
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
        
        imagecopyresampled($thumb, $img, 0, 0, (int)$cropX, (int)$cropY, $size, $size, $min, $min);

        if (!is_dir(dirname($thumbAbs))) @mkdir(dirname($thumbAbs), 0755, true);
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
        if (!$path) return '';
        $path = ltrim($path, '/');
        $info = pathinfo($path);
        $thumbRel = ($info['dirname'] === '.' ? '' : $info['dirname'] . '/') . $info['filename'] . '_thumb.webp';
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
        if (!$path) return;
        $path = ltrim($path, '/');
        $info = pathinfo($path);
        $thumbRel = ($info['dirname'] === '.' ? '' : $info['dirname'] . '/') . $info['filename'] . '_thumb.webp';
        $thumbAbs = public_path($thumbRel);
        if (file_exists($thumbAbs)) {
            @unlink($thumbAbs);
        }
    }
    /**
     * Get WebP variant if exists, otherwise return original.
     */
    public static function getWebp(string $path): string
    {
        if (!$path) return '';
        $path = ltrim($path, '/');
        $info = pathinfo($path);
        
        // Skip if already webp
        if (strtolower($info['extension'] ?? '') === 'webp') return $path;

        $webpRel = ($info['dirname'] === '.' ? '' : $info['dirname'] . '/') . $info['filename'] . '.webp';
        if (file_exists(public_path($webpRel))) {
            return $webpRel;
        }
        return $path;
    }
}
