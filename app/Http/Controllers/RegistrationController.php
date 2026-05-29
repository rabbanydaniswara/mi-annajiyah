<?php

namespace App\Http\Controllers;

use App\Helpers\DocumentHelper;
use App\Helpers\PhoneHelper;
use App\Helpers\PublicCacheHelper;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran gagal. Silakan coba lagi.',
            ], 422);
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'asal_sekolah' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:20|unique:siswa,nisn',
            'nis'  => 'nullable|string|max:20|unique:siswa,nis',
            'akte' => 'required|string|max:50',
            'kk' => 'required|string|max:50',
            'alamat' => 'required|string',
            'ortu' => 'required|string|max:100',
            'wa' => 'required|string|max:30',
            'file_akte' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'file_kk' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'file_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'file_ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
        ], [
            'nisn.unique' => 'NISN sudah terdaftar sebelumnya.',
            'nis.unique'  => 'NIS sudah terdaftar sebelumnya.',
        ]);

        $whatsapp = PhoneHelper::sanitizeIndonesianWhatsapp($request->wa);
        if (!$whatsapp) {
            throw ValidationException::withMessages([
                'wa' => 'Nomor WhatsApp harus berupa nomor Indonesia aktif, contoh: 081234567890.',
            ]);
        }

        try {
            $fileAkte = DocumentHelper::uploadPrivate($request->file('file_akte'), 'akte');
            $fileKk = DocumentHelper::uploadPrivate($request->file('file_kk'), 'kk');
            $fileKtp = DocumentHelper::uploadPrivate($request->file('file_ktp'), 'ktp');
            $fileIjazah = $request->hasFile('file_ijazah')
                ? DocumentHelper::uploadPrivate($request->file('file_ijazah'), 'ijazah')
                : null;

            $siswa = Siswa::create([
                'nama' => $request->nama,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'akte_kelahiran' => $request->akte,
                'file_akte' => $fileAkte,
                'no_kk' => $request->kk,
                'file_kk' => $fileKk,
                'alamat' => $request->alamat,
                'asal_sekolah' => $request->asal_sekolah,
                'nama_ortu' => $request->ortu,
                'file_ktp_ortu' => $fileKtp,
                'no_wa' => $whatsapp,
                'file_ijazah' => $fileIjazah,
            ]);

            \App\Helpers\ActivityLogger::log('public_registration', $siswa, "Pendaftaran baru atas nama {$siswa->nama} (Public)");
            PublicCacheHelper::clearStats();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Silakan cek status pendaftaran Anda nanti.',
                'card_url' => route('pendaftaran.print', $siswa->registration_token),
            ]);
        } catch (\Exception $e) {
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
