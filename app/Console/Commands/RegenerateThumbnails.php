<?php

namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RegenerateThumbnails extends Command
{
    protected $signature = 'app:regenerate-thumbnails {--force : Force regenerate all thumbnails}';

    protected $description = 'Regenerate image thumbnails in the uploads directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $this->info('Scanning public/uploads for webp images...');

        $files = File::allFiles(public_path('uploads'));
        $count = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'webp' && ! str_ends_with($file->getFilename(), '_thumb.webp')) {
                $relativePath = 'uploads/'.$file->getRelativePathname();
                $this->line('Processing: '.$relativePath);

                try {
                    $result = ImageHelper::generateThumbnailFor($relativePath, $force);
                    if ($result) {
                        $count++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing {$relativePath}: ".$e->getMessage());
                }
            }
        }

        $this->info("Done! Generated/checked {$count} thumbnails.");
    }
}
