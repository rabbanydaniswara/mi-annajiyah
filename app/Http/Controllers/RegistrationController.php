<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

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
            'wa' => 'required|string|max:15',
            'file_akte' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'file_kk' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'file_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'file_ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
        ], [
            'nisn.unique' => 'NISN sudah terdaftar sebelumnya.',
            'nis.unique'  => 'NIS sudah terdaftar sebelumnya.',
        ]);

        try {
            $fileAkte = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('file_akte'), 'uploads', 'akte', 70, 1600, false);
            $fileKk = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('file_kk'), 'uploads', 'kk', 70, 1600, false);
            $fileKtp = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('file_ktp'), 'uploads', 'ktp', 70, 1600, false);
            $fileIjazah = $request->hasFile('file_ijazah')
                ? \App\Helpers\ImageHelper::uploadAndOptimize($request->file('file_ijazah'), 'uploads', 'ijazah', 70, 1600, false)
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
                'no_wa' => $request->wa,
                'file_ijazah' => $fileIjazah,
            ]);

            \App\Helpers\ActivityLogger::log('public_registration', $siswa, "Pendaftaran baru atas nama {$siswa->nama} (Public)");

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! File telah dioptimasi. Silahkan cek status pendaftaran Anda nanti.',
                'id' => $siswa->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function printCard($id)
    {
        $siswa = \App\Models\Siswa::findOrFail($id);
        return view('public.cetak-kartu', compact('siswa'));
    }
}
