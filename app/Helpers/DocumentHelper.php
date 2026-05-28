<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentHelper
{
    public static function uploadPrivate(UploadedFile $file, string $prefix): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $prefix . '_' . now()->format('YmdHis') . '_' . Str::random(16) . '.' . $extension;

        Storage::disk('local')->putFileAs('ppdb', $file, $filename);

        return 'ppdb/' . $filename;
    }

    public static function absolutePath(?string $path): ?string
    {
        if (!$path) {
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

    public static function exists(?string $path): bool
    {
        $absolutePath = self::absolutePath($path);

        return $absolutePath !== null && is_file($absolutePath);
    }

    public static function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return;
        }

        $absolutePath = self::absolutePath($path);
        if ($absolutePath && is_file($absolutePath)) {
            @unlink($absolutePath);
            ImageHelper::deleteThumbnail($path);
        }
    }
}
