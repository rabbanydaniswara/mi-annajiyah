<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('registration_token', 64)->nullable()->unique()->after('id');
        });

        DB::table('siswa')->select('id')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                do {
                    $token = Str::random(40);
                } while (DB::table('siswa')->where('registration_token', $token)->exists());

                DB::table('siswa')->where('id', $row->id)->update([
                    'registration_token' => $token,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique(['registration_token']);
            $table->dropColumn('registration_token');
        });
    }
};
