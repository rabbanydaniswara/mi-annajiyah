<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class PublicCacheHelper
{
    public const KONTEN_WEB = 'public.konten_web';

    public const HOME_CONTENT = 'public.home.content';

    public const HOME_STATS = 'public.home.stats';

    public const GURU_LIST = 'public.guru.list';

    public const FASILITAS_LIST = 'public.fasilitas.list';

    public const KEGIATAN_CATEGORIES = 'public.kegiatan.categories';

    public static function clearContent(): void
    {
        foreach ([
            self::KONTEN_WEB,
            self::HOME_CONTENT,
            self::GURU_LIST,
            self::FASILITAS_LIST,
            self::KEGIATAN_CATEGORIES,
        ] as $key) {
            Cache::forget($key);
        }
    }

    public static function clearStats(): void
    {
        Cache::forget(self::HOME_STATS);
    }

    public static function clearAll(): void
    {
        self::clearContent();
        self::clearStats();
    }
}
