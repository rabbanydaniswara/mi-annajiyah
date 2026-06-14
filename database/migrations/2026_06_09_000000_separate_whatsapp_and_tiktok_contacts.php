<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $whatsapp = DB::table('konten_web')->where('tipe', 'wa')->value('konten');

        if (is_string($whatsapp) && str_contains(strtolower($whatsapp), 'tiktok.com')) {
            DB::table('konten_web')->updateOrInsert(
                ['tipe' => 'tiktok'],
                [
                    'judul' => 'TikTok',
                    'konten' => $whatsapp,
                    'urutan' => 10,
                ]
            );

            DB::table('konten_web')
                ->where('tipe', 'wa')
                ->update([
                    'judul' => 'WhatsApp',
                    'konten' => null,
                    'urutan' => 8,
                ]);
        }

        DB::table('konten_web')->where('tipe', 'ig')->update([
            'judul' => 'Instagram',
            'urutan' => 9,
        ]);
    }

    public function down(): void
    {
        $tiktok = DB::table('konten_web')->where('tipe', 'tiktok')->value('konten');
        $whatsapp = DB::table('konten_web')->where('tipe', 'wa')->value('konten');

        if (($whatsapp === null || $whatsapp === '') && is_string($tiktok) && $tiktok !== '') {
            DB::table('konten_web')->where('tipe', 'wa')->update([
                'konten' => $tiktok,
                'urutan' => 0,
            ]);
        }

        DB::table('konten_web')->where('tipe', 'tiktok')->delete();
    }
};
