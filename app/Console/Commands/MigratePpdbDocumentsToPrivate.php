<?php

namespace App\Console\Commands;

use App\Helpers\DocumentHelper;
use App\Models\Siswa;
use Illuminate\Console\Command;

class MigratePpdbDocumentsToPrivate extends Command
{
    protected $signature = 'ppdb:migrate-public-documents
        {--dry-run : Show legacy public document paths without changing files or database}
        {--keep-public : Copy documents to private storage but keep the old public files}';

    protected $description = 'Move legacy PPDB document references from public/uploads to private storage';

    private const DOCUMENT_FIELDS = ['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $deletePublic = ! $dryRun && ! (bool) $this->option('keep-public');
        $stats = ['checked' => 0, 'migrated' => 0, 'missing' => 0, 'unchanged' => 0];

        Siswa::query()
            ->select(array_merge(['id', 'nomor_pendaftaran', 'nama'], self::DOCUMENT_FIELDS))
            ->orderBy('id')
            ->chunkById(100, function ($siswas) use ($dryRun, $deletePublic, &$stats) {
                foreach ($siswas as $siswa) {
                    $updates = [];

                    foreach (self::DOCUMENT_FIELDS as $field) {
                        $path = $siswa->{$field};
                        if (! $path || ! str_starts_with(str_replace('\\', '/', ltrim($path, '/')), 'uploads/')) {
                            continue;
                        }

                        $stats['checked']++;
                        $label = ($siswa->nomor_pendaftaran ?: '#'.$siswa->id).' '.$field.' -> '.$path;

                        if ($dryRun) {
                            $this->line('[dry-run] '.$label);

                            continue;
                        }

                        $newPath = DocumentHelper::migratePublicUploadToPrivate($path, $deletePublic);
                        if (! $newPath) {
                            $stats['missing']++;
                            $this->warn('[missing] '.$label);

                            continue;
                        }

                        if ($newPath === $path) {
                            $stats['unchanged']++;

                            continue;
                        }

                        $updates[$field] = $newPath;
                        $stats['migrated']++;
                        $this->line('[migrated] '.$label.' => '.$newPath);
                    }

                    if ($updates) {
                        $siswa->forceFill($updates)->saveQuietly();
                    }
                }
            });

        $this->newLine();
        $this->info("Done. Checked: {$stats['checked']}, Migrated: {$stats['migrated']}, Missing: {$stats['missing']}, Unchanged: {$stats['unchanged']}");

        return self::SUCCESS;
    }
}
