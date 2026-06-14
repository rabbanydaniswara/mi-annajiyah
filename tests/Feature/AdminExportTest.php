<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class AdminExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_ppdb_export_downloads_real_xlsx_file(): void
    {
        $admin = User::create([
            'username' => 'admin-export-test',
            'password' => 'secret-password',
            'role' => 'admin',
        ]);

        Siswa::create([
            'nomor_pendaftaran' => 'PPDB-2026-0001',
            'tahun_ajaran' => '2026/2027',
            'nama' => 'Alya Export',
            'nisn' => '0012345678',
            'nis' => 'NIS-EXPORT-01',
            'kelas' => '1A',
            'no_wa' => '081234567890',
            'nama_ortu' => 'Wali Export',
            'status_ppdb' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.export', [
            'type' => 'ppdb',
            'q' => 'Alya',
        ]));
        $downloadPath = $response->baseResponse->getFile()->getPathname();

        try {
            $response->assertOk();
            $response->assertDownload();
            $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

            $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
            $this->assertSame('PK', substr(File::get($downloadPath), 0, 2));

            $spreadsheet = IOFactory::load($downloadPath);
            $sheet = $spreadsheet->getSheetByName('Data PPDB');

            $this->assertNotNull($sheet);
            $this->assertSame('Data PPDB MI Annajiyah', $sheet->getCell('A1')->getValue());
            $this->assertSame('Filter: Pencarian "Alya"', $sheet->getCell('A4')->getValue());
            $this->assertSame('No Pendaftaran', $sheet->getCell('B6')->getValue());
            $this->assertSame('0012345678', $sheet->getCell('F7')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('F7')->getDataType());
            $this->assertSame('081234567890', $sheet->getCell('M7')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('M7')->getDataType());
            $this->assertSame('A7', $sheet->getFreezePane());
            $this->assertSame('A6:R7', $sheet->getAutoFilter()->getRange());
        } finally {
            if ($downloadPath && File::exists($downloadPath)) {
                File::delete($downloadPath);
            }
        }
    }
}
