@extends('layouts.admin')
@section('title', 'Kelola Fasilitas')
@section('header_icon', 'school')
@section('header_title', 'Manajemen Fasilitas')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold"><i class="fas fa-school mr-2"></i>Kelola Fasilitas Sekolah</h3>
    <p class="text-green-200 mt-1">Tambah, edit, dan kelola fasilitas yang ditampilkan di halaman publik.</p>
</div>

<a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2 rounded-xl font-semibold hover:bg-[var(--color-accent-dark)] transition mb-6 text-sm">
    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form Tambah/Edit --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade sticky top-6">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas fa-{{ $edit ? 'edit' : 'plus' }} mr-2"></i>{{ $edit ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}
            </h3>
            <form method="POST" action="{{ route('admin.fasilitas.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @if($edit)<input type="hidden" name="id" value="{{ $edit->id }}">@endif

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Fasilitas *</label>
                    <input type="text" name="nama" value="{{ $edit->nama ?? '' }}" required
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"
                           placeholder="Contoh: Ruang Kelas">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm resize-none"
                              placeholder="Deskripsi singkat fasilitas...">{{ $edit->deskripsi ?? '' }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ikon <span class="text-gray-400">(Font Awesome class)</span></label>
                    <input type="text" name="ikon" value="{{ $edit->ikon ?? 'fas fa-school' }}"
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"
                           placeholder="fas fa-chalkboard">
                    <p class="text-xs text-gray-400 mt-1">Contoh: fas fa-book, fas fa-futbol, fas fa-utensils</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="urutan" value="{{ $edit->urutan ?? 0 }}" min="0"
                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Fasilitas <span class="text-gray-400">(opsional)</span></label>
                    <input type="file" name="gambar" accept="image/*"
                           class="w-full text-sm file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-[var(--color-accent)] file:text-[var(--color-primary)] file:font-semibold file:cursor-pointer">
                    @if($edit && $edit->gambar)
                    <img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($edit->gambar)) }}" class="w-24 h-16 object-cover rounded-lg mt-2">
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="aktif" id="aktif" value="1" {{ (!$edit || $edit->aktif) ? 'checked' : '' }} class="w-4 h-4 rounded accent-[var(--color-accent)]">
                    <label for="aktif" class="text-sm font-semibold text-gray-700">Tampilkan di website</label>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-[var(--color-primary)] text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm">
                        <i class="fas fa-save mr-1"></i> {{ $edit ? 'Update' : 'Simpan' }}
                    </button>
                    @if($edit)
                    <a href="{{ route('admin.fasilitas') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm">Batal</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Fasilitas --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas fa-list mr-2"></i>Daftar Fasilitas ({{ $fasilitas->count() }})
            </h3>
            @if($fasilitas->isEmpty())
            <p class="text-center text-gray-400 py-8">Belum ada fasilitas. Tambahkan di form sebelah kiri.</p>
            @else
            <div class="space-y-3">
                @foreach($fasilitas as $fas)
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                    <div class="w-12 h-12 bg-[var(--color-accent)]/10 rounded-xl flex items-center justify-center shrink-0">
                        @if($fas->gambar)
                        <img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($fas->gambar)) }}" class="w-12 h-12 object-cover rounded-xl">
                        @else
                        <i class="{{ $fas->ikon ?? 'fas fa-school' }} text-xl text-[var(--color-accent)]"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <strong class="text-[var(--color-primary)] text-sm">{{ $fas->nama }}</strong>
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $fas->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                {{ $fas->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $fas->deskripsi }}</p>
                        <p class="text-gray-400 text-xs"><i class="text-[var(--color-accent)]"></i> Urutan: {{ $fas->urutan }}</p>
                    </div>
                    <div class="flex gap-1 shrink-0">
                        <form method="POST" action="{{ route('admin.fasilitas.toggle', $fas->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" title="Toggle Aktif"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center transition {{ $fas->aktif ? 'bg-blue-100 text-blue-600 hover:bg-blue-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <i class="fas fa-toggle-{{ $fas->aktif ? 'on' : 'off' }} text-sm"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.fasilitas', ['edit' => $fas->id]) }}" title="Edit"
                           class="w-8 h-8 bg-yellow-100 text-yellow-600 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.fasilitas.destroy', $fas->id) }}" class="inline" 
                              data-confirm="Yakin ingin menghapus fasilitas {{ $fas->nama }}?"
                              data-title="Konfirmasi Hapus"
                              data-button="Hapus"
                              data-type="danger">
                            @csrf @method('DELETE')
                            <button type="submit" title="Hapus"
                                    class="w-8 h-8 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg flex items-center justify-center transition">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
