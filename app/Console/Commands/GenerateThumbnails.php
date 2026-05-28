<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ImageHelper;
use App\Models\{Guru, KegiatanSekolah, Banner, Fasilitas};

class GenerateThumbnails extends Command
{
    protected $signature = 'thumbnails:generate {--force : Regenerate even if thumb exists}';
    protected $description = 'Generate _thumb.webp variants for guru/kegiatan/banner/fasilitas images';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $stats = ['ok' => 0, 'skip' => 0, 'fail' => 0];

        $sources = [
            'guru'      => Guru::pluck('foto')->filter()->values(),
            'kegiatan'  => KegiatanSekolah::pluck('gambar')->filter()->values(),
            'banner'    => Banner::pluck('gambar')->filter()->values(),
            'fasilitas' => Fasilitas::pluck('gambar')->filter()->values(),
        ];

        foreach ($sources as $kind => $paths) {
            $this->info("Processing {$kind} (" . $paths->count() . ")");
            foreach ($paths as $p) {
                $thumb = preg_replace('/\.[^.]+$/', '_thumb.webp', ltrim($p, '/'));
                $exists = file_exists(public_path($thumb));
                if ($exists && !$force) { $stats['skip']++; continue; }

                $ok = ImageHelper::generateThumbnailFor($p, $force);
                if ($ok) {
                    $stats['ok']++;
                    $this->line("  <fg=green>✓</> $p");
                } else {
                    $stats['fail']++;
                    $this->line("  <fg=red>✗</> $p");
                }
            }
        }

        $this->newLine();
        $this->info("Done. Generated: {$stats['ok']}, Skipped: {$stats['skip']}, Failed: {$stats['fail']}");
        return self::SUCCESS;
    }
}
