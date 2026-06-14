<?php

namespace App\Console\Commands;

use App\Helpers\DocumentHelper;
use App\Models\Siswa;
use Illuminate\Console\Command;

class GeneratePpdbDocumentThumbnails extends Command
{
    protected $signature = 'ppdb:generate-document-thumbnails {--force : Regenerate existing thumbnails}';

    protected $description = 'Generate private lightweight thumbnails for PPDB image documents';

    private const DOCUMENT_FIELDS = ['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'];

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $stats = ['checked' => 0, 'generated' => 0, 'skipped' => 0, 'failed' => 0];

        Siswa::query()
            ->select(array_merge(['id', 'nomor_pendaftaran'], self::DOCUMENT_FIELDS))
            ->orderBy('id')
            ->chunkById(100, function ($siswas) use ($force, &$stats) {
                foreach ($siswas as $siswa) {
                    foreach (self::DOCUMENT_FIELDS as $field) {
                        $path = $siswa->{$field};
                        if (! DocumentHelper::isImage($path)) {
                            $stats['skipped']++;

                            continue;
                        }

                        $stats['checked']++;
                        $thumb = DocumentHelper::ensureThumbnail($path, $force);
                        if ($thumb) {
                            $stats['generated']++;
                            $this->line('[ok] '.($siswa->nomor_pendaftaran ?: '#'.$siswa->id).' '.$field.' => '.$thumb);
                        } else {
                            $stats['failed']++;
                            $this->warn('[failed] '.($siswa->nomor_pendaftaran ?: '#'.$siswa->id).' '.$field.' -> '.$path);
                        }
                    }
                }
            });

        $this->newLine();
        $this->info("Done. Checked: {$stats['checked']}, Generated/ready: {$stats['generated']}, Skipped: {$stats['skipped']}, Failed: {$stats['failed']}");

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
