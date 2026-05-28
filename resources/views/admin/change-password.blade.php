@extends('layouts.admin')
@section('title', 'Ganti Password')
@section('header_icon', 'key')
@section('header_title', 'Ganti Password')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold relative z-10"><i class="fas fa-key mr-2"></i>Ganti Password</h3>
    <p class="text-green-200 mt-1 relative z-10">Perbarui password akun Anda secara mandiri.</p>
</div>

<div class="bg-white rounded-2xl p-6 shadow-sm max-w-2xl">
    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-bold mb-1">Password belum bisa diperbarui.</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-[var(--color-primary)] mb-1">Password Lama</label>
            <input type="password" name="current_password" required autocomplete="current-password" class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-[var(--color-accent)] outline-none transition">
        </div>

        <div>
            <label class="block text-sm font-semibold text-[var(--color-primary)] mb-1">Password Baru</label>
            <input type="password" name="password" required autocomplete="new-password" class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-[var(--color-accent)] outline-none transition">
            <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-[var(--color-primary)] mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-[var(--color-accent)] outline-none transition">
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] text-white px-6 py-3 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition">
                <i class="fas fa-save"></i> Simpan Password
            </button>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-600 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
