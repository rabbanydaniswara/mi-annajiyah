<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Helpers\DocumentHelper;
use App\Helpers\PhoneHelper;
use App\Helpers\PpdbHelper;
use App\Helpers\PublicCacheHelper;
use App\Http\Requests\StorePpdbRegistrationRequest;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegistrationController extends Controller
{
    public function store(StorePpdbRegistrationRequest $request)
    {
        if ($request->filled('website')) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran gagal. Silakan coba lagi.',
            ], 422);
        }

        $validated = $request->validated();
        $whatsapp = PhoneHelper::sanitizeIndonesianWhatsapp($validated['wa']);

        $uploadedDocuments = [];

        try {
            $uploadedDocuments['file_akte'] = DocumentHelper::uploadPrivate($request->file('file_akte'), 'akte');
            $uploadedDocuments['file_kk'] = DocumentHelper::uploadPrivate($request->file('file_kk'), 'kk');
            $uploadedDocuments['file_ktp_ortu'] = DocumentHelper::uploadPrivate($request->file('file_ktp'), 'ktp');

            if ($request->hasFile('file_ijazah')) {
                $uploadedDocuments['file_ijazah'] = DocumentHelper::uploadPrivate(
                    $request->file('file_ijazah'),
                    'ijazah'
                );
            }

            $siswa = PpdbHelper::createSiswa([
                'nama' => $validated['nama'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'nisn' => $validated['nisn'] ?? null,
                'nis' => $validated['nis'] ?? null,
                'akte_kelahiran' => $validated['akte'],
                'file_akte' => $uploadedDocuments['file_akte'],
                'no_kk' => $validated['kk'],
                'file_kk' => $uploadedDocuments['file_kk'],
                'alamat' => $validated['alamat'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'nama_ortu' => $validated['ortu'],
                'file_ktp_ortu' => $uploadedDocuments['file_ktp_ortu'],
                'no_wa' => $whatsapp,
                'file_ijazah' => $uploadedDocuments['file_ijazah'] ?? null,
            ]);

            ActivityLogger::log('public_registration', $siswa, "Pendaftaran baru atas nama {$siswa->nama} (Public)");
            PublicCacheHelper::clearStats();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Silakan cek status pendaftaran Anda nanti.',
                'card_url' => route('pendaftaran.print', $siswa->registration_token),
            ]);
        } catch (Throwable $e) {
            foreach ($uploadedDocuments as $path) {
                DocumentHelper::delete($path);
            }

            Log::error('Public registration failed', [
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi atau hubungi panitia.',
            ], 500);
        }
    }

    public function printCard($token)
    {
        $siswa = Siswa::where('registration_token', $token)->firstOrFail();

        return view('public.cetak-kartu', compact('siswa'));
    }
}
