<?php

namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use App\Models\{Banner, Fasilitas, Guru, KegiatanSekolah};
use Illuminate\Console\Command;

class GenerateMediaVariants extends Command
{
    protected $signature = 'media:generate-variants {--force : Regenerate variants even if they already exist}';
    protected $description = 'Generate lightweight card and hero WebP variants for public media';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $stats = ['ok' => 0, 'skip' => 0, 'fail' => 0];

        $groups = [
            'banner hero' => [
                'paths' => Banner::pluck('gambar')->filter()->unique()->values(),
                'suffix' => 'hero',
                'width' => 1600,
                'quality' => 72,
            ],
            'banner card' => [
                'paths' => Banner::pluck('gambar')->filter()->unique()->values(),
                'suffix' => 'card',
                'width' => 640,
                'quality' => 68,
            ],
            'fasilitas card' => [
                'paths' => Fasilitas::pluck('gambar')->filter()->unique()->values(),
                'suffix' => 'card',
                'width' => 560,
                'quality' => 64,
            ],
            'kegiatan card' => [
                'paths' => KegiatanSekolah::pluck('gambar')->filter()->unique()->values(),
                'suffix' => 'card',
                'width' => 420,
                'quality' => 45,
            ],
            'guru card' => [
                'paths' => Guru::pluck('foto')->filter()->unique()->values(),
                'suffix' => 'card',
                'width' => 480,
                'quality' => 64,
            ],
        ];

        foreach ($groups as $label => $config) {
            $this->info('Processing ' . $label . ' (' . $config['paths']->count() . ')');

            foreach ($config['paths'] as $path) {
                $variant = preg_replace('/\.[^.]+$/', '_' . $config['suffix'] . '.webp', ltrim($path, '/'));
                if (!$force && file_exists(public_path($variant))) {
                    $stats['skip']++;
                    continue;
                }

                $ok = ImageHelper::generateVariantFor($path, $config['suffix'], $config['width'], $config['quality'], $force);
                if ($ok) {
                    $stats['ok']++;
                    $this->line('  <fg=green>OK</> ' . $variant);
                } else {
                    $stats['fail']++;
                    $this->line('  <fg=red>FAIL</> ' . $path);
                }
            }
        }

        $this->newLine();
        $this->info("Done. Generated: {$stats['ok']}, Skipped: {$stats['skip']}, Failed: {$stats['fail']}");

        return self::SUCCESS;
    }
}
