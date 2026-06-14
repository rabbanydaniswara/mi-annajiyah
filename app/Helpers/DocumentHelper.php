<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class DocumentHelper
{
    private const PPDB_DIRECTORY = 'ppdb';

    private const PPDB_THUMB_DIRECTORY = 'ppdb-thumbs';

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public static function uploadPrivate(UploadedFile $file, string $prefix): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $prefix.'_'.now()->format('YmdHis').'_'.Str::random(16).'.'.$extension;
        $path = self::PPDB_DIRECTORY.'/'.$filename;

        $storedPath = Storage::disk('local')->putFileAs(self::PPDB_DIRECTORY, $file, $filename);
        if ($storedPath !== $path) {
            throw new RuntimeException('Dokumen PPDB gagal disimpan.');
        }

        self::ensureThumbnail($path);

        return $path;
    }

    public static function absolutePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        $publicPath = public_path($path);
        $realPublicPath = realpath(public_path());
        $realFilePath = realpath($publicPath);

        if ($realPublicPath && $realFilePath && str_starts_with($realFilePath, $realPublicPath)) {
            return $realFilePath;
        }

        return null;
    }

    public static function isImage(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::IMAGE_EXTENSIONS, true);
    }

    public static function ensureThumbnail(?string $path, bool $force = false): ?string
    {
        if (! self::isImage($path)) {
            return null;
        }

        $path = ltrim((string) $path, '/');
        $absolutePath = self::absolutePath($path);
        if (! $absolutePath || ! is_file($absolutePath)) {
            return null;
        }

        $thumbnailPath = self::thumbnailPath($path);
        if (! $force && Storage::disk('local')->exists($thumbnailPath)) {
            return $thumbnailPath;
        }

        $image = self::loadImageResource($absolutePath, pathinfo($path, PATHINFO_EXTENSION));
        if (! $image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $maxDimension = 360;
        $scale = min($maxDimension / max($width, 1), $maxDimension / max($height, 1), 1);
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefill($thumb, 0, 0, $white);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $thumbnailAbsolutePath = Storage::disk('local')->path($thumbnailPath);
        if (! is_dir(dirname($thumbnailAbsolutePath))) {
            mkdir(dirname($thumbnailAbsolutePath), 0755, true);
        }

        $ok = imagewebp($thumb, $thumbnailAbsolutePath, 62);

        imagedestroy($image);
        imagedestroy($thumb);

        return $ok ? $thumbnailPath : null;
    }

    public static function thumbnailAbsolutePath(?string $path): ?string
    {
        $thumbnailPath = self::ensureThumbnail($path);

        if (! $thumbnailPath || ! Storage::disk('local')->exists($thumbnailPath)) {
            return null;
        }

        return Storage::disk('local')->path($thumbnailPath);
    }

    public static function migratePublicUploadToPrivate(?string $path, bool $deletePublic = true): ?string
    {
        if (! $path) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        if (! str_starts_with($path, 'uploads/')) {
            return $path;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $baseName = Str::slug(pathinfo($path, PATHINFO_FILENAME)) ?: 'document';
        $targetPath = self::PPDB_DIRECTORY.'/legacy_'.substr(sha1($path), 0, 12).'_'.$baseName.'.'.$extension;

        if (Storage::disk('local')->exists($targetPath)) {
            self::ensureThumbnail($targetPath);

            return $targetPath;
        }

        $publicPath = self::publicUploadAbsolutePath($path);
        if (! $publicPath || ! is_file($publicPath)) {
            return null;
        }

        if (! Storage::disk('local')->exists($targetPath)) {
            Storage::disk('local')->put($targetPath, file_get_contents($publicPath));
        }

        self::ensureThumbnail($targetPath);

        if ($deletePublic) {
            ImageHelper::deleteImageSet($path, ['thumb']);
        }

        return $targetPath;
    }

    public static function exists(?string $path): bool
    {
        $absolutePath = self::absolutePath($path);

        return $absolutePath !== null && is_file($absolutePath);
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('local')->exists($path)) {
            self::deleteThumbnail($path);
            Storage::disk('local')->delete($path);

            return;
        }

        $absolutePath = self::absolutePath($path);
        if ($absolutePath && is_file($absolutePath)) {
            self::deleteThumbnail($path);
            @unlink($absolutePath);
            ImageHelper::deleteThumbnail($path);
        }
    }

    public static function deleteThumbnail(?string $path): void
    {
        if (! $path || ! self::isImage($path)) {
            return;
        }

        $thumbnailPath = self::thumbnailPath(ltrim($path, '/'));
        if (Storage::disk('local')->exists($thumbnailPath)) {
            Storage::disk('local')->delete($thumbnailPath);
        }
    }

    private static function thumbnailPath(string $path): string
    {
        $baseName = Str::slug(pathinfo($path, PATHINFO_FILENAME)) ?: 'document';

        return self::PPDB_THUMB_DIRECTORY.'/'.substr(sha1($path), 0, 12).'_'.$baseName.'_thumb.webp';
    }

    private static function publicUploadAbsolutePath(string $path): ?string
    {
        $publicUploadsRoot = realpath(public_path('uploads'));
        $realFilePath = realpath(public_path($path));

        if (! $publicUploadsRoot || ! $realFilePath) {
            return null;
        }

        $root = rtrim(strtolower(str_replace('\\', '/', $publicUploadsRoot)), '/');
        $file = strtolower(str_replace('\\', '/', $realFilePath));

        return str_starts_with($file, $root.'/') ? $realFilePath : null;
    }

    private static function loadImageResource(string $absolutePath, ?string $extension)
    {
        return match (strtolower((string) $extension)) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absolutePath),
            'png' => self::loadPng($absolutePath),
            'webp' => @imagecreatefromwebp($absolutePath),
            default => false,
        };
    }

    private static function loadPng(string $absolutePath)
    {
        $image = @imagecreatefrompng($absolutePath);
        if ($image) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        return $image;
    }
}
