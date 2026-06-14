@extends('layouts.admin')
@section('title', 'Kelola Konten')
@section('header_icon', 'edit')
@section('header_title', 'Manajemen Konten')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold"><i class="fas fa-edit mr-2"></i>Kelola Konten Website</h3>
    <p class="text-green-200 mt-1">Kelola visi, misi, sejarah, kegiatan sekolah (dengan kategori), dan banner slider.</p>
</div>

<a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2 rounded-xl font-semibold hover:bg-[var(--color-accent-dark)] transition mb-6 text-sm">
    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
</a>

{{-- Tab Navigation --}}
<div class="flex gap-2 mb-6 overflow-x-auto pb-1" x-data="{ tab: '{{ request('tab', 'visi') }}' }">
    @foreach(['visi' => 'Visi & Misi', 'sejarah' => 'Sejarah', 'kegiatan' => 'Kegiatan', 'banner' => 'Banner', 'ppdb' => 'PPDB', 'kontak' => 'Kontak'] as $key => $label)
    <a href="{{ route('admin.konten', ['tab' => $key]) }}"
       class="px-5 py-2 rounded-xl font-semibold text-sm whitespace-nowrap transition {{ request('tab', 'visi') === $key ? 'bg-[var(--color-primary)] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 shadow-sm' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

@php $activeTab = request('tab', 'visi'); @endphp

{{-- ===== TAB: VISI & MISI ===== --}}
@if($activeTab === 'visi')
<div class="space-y-6 animate-fade">
    {{-- Visi --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-eye mr-2"></i>Visi Madrasah</h3>
        <form method="POST" action="{{ route('admin.konten.update') }}">
            @csrf
            <textarea name="konten" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/20 outline-none transition">{{ $visi->konten ?? '' }}</textarea>
            <input type="hidden" name="tipe" value="visi">
            <button type="submit" class="mt-3 bg-[var(--color-primary)] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> Simpan Visi</button>
        </form>
    </div>
    {{-- Misi --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-bullseye mr-2"></i>Misi Madrasah</h3>
        <form method="POST" action="{{ route('admin.konten.update') }}">
            @csrf
            <textarea name="konten" rows="6" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/20 outline-none transition">{{ $misi->konten ?? '' }}</textarea>
            <input type="hidden" name="tipe" value="misi">
            <button type="submit" class="mt-3 bg-[var(--color-primary)] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> Simpan Misi</button>
        </form>
    </div>
</div>
@endif

{{-- ===== TAB: SEJARAH ===== --}}
@if($activeTab === 'sejarah')
<div class="bg-white rounded-2xl p-6 shadow-sm animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-history mr-2"></i>Sejarah Madrasah</h3>
    <form method="POST" action="{{ route('admin.konten.update') }}">
        @csrf
        <textarea name="konten" rows="8" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition">{{ $sejarah->konten ?? '' }}</textarea>
        <input type="hidden" name="tipe" value="sejarah">
        <button type="submit" class="mt-3 bg-[var(--color-primary)] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> Simpan Sejarah</button>
    </form>
</div>
@endif

{{-- ===== TAB: KEGIATAN ===== --}}
@if($activeTab === 'kegiatan')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade">
    <div class="lg:col-span-1 space-y-6">
        {{-- Form Tambah Kegiatan --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-{{ $editKegiatan ? 'edit' : 'plus' }} mr-2"></i>{{ $editKegiatan ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</h3>
            <form method="POST" action="{{ route('admin.konten.storeKegiatan') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @if($editKegiatan)<input type="hidden" name="id" value="{{ $editKegiatan->id }}">@endif
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Kegiatan *</label>
                    <input type="text" name="judul" value="{{ old('judul', $editKegiatan->judul ?? '') }}" required placeholder="Judul kegiatan" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal *</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $editKegiatan?->tanggal?->format('Y-m-d')) }}" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
                    <select name="kategori_id" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                        <option value="">-- Tanpa Kategori --</option>
                        @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ (string) old('kategori_id', $editKegiatan->kategori_id ?? '') === (string) $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Foto Kegiatan</label>
                    <input type="file" name="gambar_kegiatan" accept="image/*" class="w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[var(--color-accent)] file:text-[var(--color-primary)] file:font-semibold file:cursor-pointer">
                    @if($editKegiatan?->gambar)
                    <img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($editKegiatan->gambar)) }}" alt="" class="w-20 h-20 object-cover rounded-xl mt-2">
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat..." class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm resize-none">{{ old('deskripsi', $editKegiatan->deskripsi ?? '') }}</textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-[var(--color-accent)] text-[var(--color-primary)] py-2 rounded-xl font-semibold hover:bg-[var(--color-accent-dark)] transition text-sm"><i class="fas fa-{{ $editKegiatan ? 'save' : 'plus' }} mr-1"></i> {{ $editKegiatan ? 'Update' : 'Tambah' }}</button>
                    @if($editKegiatan)<a href="{{ route('admin.konten', ['tab' => 'kegiatan']) }}" class="px-3 py-2 bg-gray-200 text-gray-600 rounded-xl text-sm font-semibold">Batal</a>@endif
                </div>
            </form>
        </div>

        {{-- Kelola Kategori --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-tags mr-2"></i>Kategori Kegiatan</h3>
            <form method="POST" action="{{ route('admin.konten.storeKategori') }}" class="flex gap-2 mb-4">
                @csrf
                @if($editKategori)<input type="hidden" name="id" value="{{ $editKategori->id }}">@endif
                <input type="text" name="nama" value="{{ old('nama', $editKategori->nama ?? '') }}" required placeholder="Nama kategori" class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                <button type="submit" class="bg-[var(--color-primary)] text-white px-3 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm" title="{{ $editKategori ? 'Update kategori' : 'Tambah kategori' }}"><i class="fas fa-{{ $editKategori ? 'save' : 'plus' }}"></i></button>
                @if($editKategori)<a href="{{ route('admin.konten', ['tab' => 'kegiatan']) }}" class="bg-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm"><i class="fas fa-times"></i></a>@endif
            </form>
            @foreach($kategoris as $kat)
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-700">{{ $kat->nama }}</span>
                <span class="text-xs text-gray-400 mr-auto ml-2">({{ $kat->kegiatan->count() }})</span>
                <a href="{{ route('admin.konten', ['tab' => 'kegiatan', 'edit_kategori' => $kat->id]) }}" class="text-yellow-500 hover:text-yellow-700 transition text-xs mr-2" title="Edit kategori"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{ route('admin.konten.destroyKategori', $kat->id) }}" class="inline" 
                      data-confirm="Yakin ingin menghapus kategori '{{ $kat->nama }}'?"
                      data-title="Hapus Kategori"
                      data-button="Hapus"
                      data-type="danger">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 transition text-xs"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            @endforeach
            @if($kategoris->isEmpty())<p class="text-xs text-gray-400 text-center py-2">Belum ada kategori.</p>@endif
        </div>
    </div>

    {{-- Daftar Kegiatan --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-calendar-alt mr-2"></i>Daftar Kegiatan ({{ $kegiatan->count() }})</h3>
            @if($kegiatan->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-2xl text-green-300"></i>
                </div>
                <p class="text-gray-400 font-medium mb-1">Belum ada kegiatan.</p>
                <p class="text-gray-300 text-sm">Tambahkan kegiatan sekolah di form sebelah kiri.</p>
            </div>
            @else
            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                @foreach($kegiatan as $kgt)
                <div class="flex gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                    @if($kgt->gambar)
                    <img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($kgt->gambar)) }}" alt="" class="w-16 h-16 object-cover rounded-xl shrink-0">
                    @else
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center shrink-0"><i class="fas fa-calendar-alt text-green-400 text-xl"></i></div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <strong class="text-[var(--color-primary)] text-sm block truncate">{{ $kgt->judul }}</strong>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <small class="text-gray-400 text-xs"><i class="far fa-calendar-alt mr-1"></i>{{ $kgt->tanggal?->format('d M Y') }}</small>
                            @if($kgt->kategori)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">{{ $kgt->kategori->nama }}</span>
                            @endif
                        </div>
                        @if($kgt->deskripsi)<p class="text-gray-400 text-xs mt-1 truncate">{{ $kgt->deskripsi }}</p>@endif
                    </div>
                    <div class="shrink-0 flex gap-1">
                    <a href="{{ route('admin.konten', ['tab' => 'kegiatan', 'edit_kegiatan' => $kgt->id]) }}" class="w-8 h-8 bg-yellow-100 text-yellow-600 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition text-xs" title="Edit kegiatan"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{ route('admin.konten.destroyKegiatan', $kgt->id) }}"
                          data-confirm="Yakin ingin menghapus kegiatan '{{ $kgt->judul }}'?"
                          data-title="Hapus Kegiatan"
                          data-button="Hapus"
                          data-type="danger">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-8 h-8 bg-red-100 text-red-500 hover:bg-red-200 rounded-lg flex items-center justify-center transition text-xs"><i class="fas fa-trash"></i></button>
                    </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ===== TAB: BANNER ===== --}}
@if($activeTab === 'banner')
<div class="bg-white rounded-2xl p-6 shadow-sm animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-images mr-2"></i>Banner Slider (Hero)</h3>
    <form method="POST" action="{{ route('admin.konten.storeBanner') }}" enctype="multipart/form-data" class="bg-gray-50 rounded-xl p-4 mb-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input type="text" name="judul_banner" placeholder="Judul Banner *" required class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
            <input type="text" name="subtitle_banner" placeholder="Subtitle Banner" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
            <input type="number" name="urutan_banner" placeholder="Urutan" value="1" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
            <input type="file" name="gambar_banner" accept="image/*" required class="text-sm file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-[var(--color-accent)] file:text-[var(--color-primary)] file:font-semibold file:cursor-pointer">
        </div>
        <button type="submit" class="mt-3 bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2 rounded-xl font-semibold hover:bg-[var(--color-accent-dark)] transition text-sm"><i class="fas fa-plus mr-1"></i> Tambah Banner</button>
    </form>

    <div class="space-y-3">
        @foreach($banners as $banner)
        <div class="flex flex-wrap items-center gap-4 py-3 px-4 border border-gray-100 rounded-xl hover:bg-yellow-50/50 transition">
            <div class="shrink-0">
                @if($banner->gambar)
                <img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($banner->gambar)) }}" class="w-20 h-12 object-cover rounded-lg">
                @else
                <div class="w-20 h-12 bg-gray-200 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>
                @endif
            </div>
            <div class="flex-1">
                <strong class="text-[var(--color-primary)] text-sm">{{ $banner->judul }}</strong><br>
                <small class="text-gray-500">{{ $banner->subtitle }}</small>
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold {{ $banner->aktif ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">{{ $banner->aktif ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <div class="flex gap-1">
                <form method="POST" action="{{ route('admin.konten.toggleBanner', $banner->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-8 h-8 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-lg flex items-center justify-center text-xs transition" title="Ubah status"><i class="fas fa-toggle-on"></i></button>
                </form>
                <a href="{{ route('admin.konten', ['edit_banner' => $banner->id, 'tab' => 'banner']) }}" class="w-8 h-8 bg-yellow-100 text-yellow-600 hover:bg-yellow-200 rounded-lg flex items-center justify-center text-xs transition"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{ route('admin.konten.destroyBanner', $banner->id) }}" class="inline" 
                      data-confirm="Yakin ingin menghapus banner '{{ $banner->judul }}'?"
                      data-title="Hapus Banner"
                      data-button="Hapus"
                      data-type="danger">
@csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg flex items-center justify-center text-xs transition"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
        @endforeach
        @if($banners->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-images text-2xl text-yellow-300"></i>
            </div>
            <p class="text-gray-400 font-medium mb-1">Belum ada banner.</p>
            <p class="text-gray-300 text-sm">Tambahkan banner hero di form di atas.</p>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Edit Banner Modal --}}
@if($editBanner)
<div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full max-h-[85vh] overflow-y-auto shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[var(--color-primary)]"><i class="fas fa-edit mr-2"></i>Edit Banner</h3>
            <a href="{{ route('admin.konten', ['tab' => 'banner']) }}" class="text-gray-400 hover:text-red-500 text-2xl transition">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.konten.updateBanner') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="banner_id" value="{{ $editBanner->id }}">
            <div class="space-y-4">
                <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1">Judul</label><input type="text" name="judul_banner" value="{{ $editBanner->judul }}" required class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
                <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1">Subtitle</label><input type="text" name="subtitle_banner" value="{{ $editBanner->subtitle }}" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
                <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1">Urutan</label><input type="number" name="urutan_banner" value="{{ $editBanner->urutan }}" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
                <div>
                    <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1">Gambar (kosongkan jika tidak diubah)</label>
                    <input type="file" name="gambar_banner_edit" accept="image/*" class="w-full text-sm">
                    @if($editBanner->gambar)<img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($editBanner->gambar)) }}" class="w-24 rounded-lg mt-2">@endif
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="bg-[var(--color-primary)] text-white px-5 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> Update</button>
                <a href="{{ route('admin.konten', ['tab' => 'banner']) }}" class="bg-gray-500 text-white px-5 py-2 rounded-xl font-semibold hover:bg-gray-600 transition text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endif
@if($activeTab === 'ppdb')
<div class="space-y-5 animate-fade">
    <div class="rounded-2xl border p-5 flex flex-col sm:flex-row sm:items-center gap-4 {{ $ppdbSettings['is_open'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $ppdbSettings['is_open'] ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
            <i class="fas {{ $ppdbSettings['is_open'] ? 'fa-lock-open' : 'fa-lock' }}"></i>
        </div>
        <div>
            <p class="text-xs font-black uppercase tracking-widest {{ $ppdbSettings['is_open'] ? 'text-green-600' : 'text-red-600' }}">Status Saat Ini</p>
            <h3 class="text-xl font-black {{ $ppdbSettings['is_open'] ? 'text-green-800' : 'text-red-800' }}">
                Pendaftaran PPDB {{ $ppdbSettings['is_open'] ? 'Dibuka' : 'Ditutup' }}
            </h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-user-graduate mr-2"></i>Pengaturan PPDB</h3>
        <p class="text-xs text-gray-400 mb-6">Perubahan status langsung berlaku pada halaman publik dan endpoint pendaftaran. Data pendaftar yang sudah ada tetap aman.</p>

        <form method="POST" action="{{ route('admin.konten.update') }}" class="space-y-5 max-w-2xl">
            @csrf
            <input type="hidden" name="tipe" value="ppdb_settings">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pendaftaran</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="status_pendaftaran" value="open" class="peer sr-only" {{ old('status_pendaftaran', $ppdbSettings['is_open'] ? 'open' : 'closed') === 'open' ? 'checked' : '' }}>
                        <span class="flex items-center gap-3 rounded-2xl border-2 border-gray-200 p-4 peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                            <i class="fas fa-lock-open text-green-600"></i>
                            <span><strong class="block text-sm text-gray-800">Buka PPDB</strong><small class="text-gray-500">Form dapat diisi dan dikirim.</small></span>
                        </span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_pendaftaran" value="closed" class="peer sr-only" {{ old('status_pendaftaran', $ppdbSettings['is_open'] ? 'open' : 'closed') === 'closed' ? 'checked' : '' }}>
                        <span class="flex items-center gap-3 rounded-2xl border-2 border-gray-200 p-4 peer-checked:border-red-500 peer-checked:bg-red-50 transition">
                            <i class="fas fa-lock text-red-600"></i>
                            <span><strong class="block text-sm text-gray-800">Tutup PPDB</strong><small class="text-gray-500">Form dan submit publik dinonaktifkan.</small></span>
                        </span>
                    </label>
                </div>
                @error('status_pendaftaran')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Ajaran Aktif</label>
                <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $ppdbSettings['academic_year']) }}" required pattern="\d{4}[/-]\d{4}" placeholder="2026/2027" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                <p class="text-xs text-gray-400 mt-1">Nomor pendaftaran baru memakai awalan tahun pertama, misalnya PPDB-2026-0001.</p>
                @error('tahun_ajaran')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pesan Publik Saat Ditutup</label>
                <textarea name="pesan_tutup" rows="4" maxlength="1000" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm resize-y" placeholder="Jelaskan status dan cara menghubungi panitia.">{{ old('pesan_tutup', $ppdbSettings['closed_message']) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Pesan ini tampil pada halaman pendaftaran dan dikirim saat submit ditolak.</p>
                @error('pesan_tutup')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-[var(--color-primary)] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[var(--color-primary-light)] transition shadow-md">
                <i class="fas fa-save mr-2"></i> Simpan Pengaturan PPDB
            </button>
        </form>
    </div>
</div>
@endif
@if($activeTab === 'kontak')
<div class="bg-white rounded-2xl p-6 shadow-sm animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-address-book mr-2"></i>Kontak & Sosial Media</h3>
    <p class="text-xs text-gray-400 mb-6">Data ini akan ditampilkan di footer dan bagian kontak website.</p>
    
    <form method="POST" action="{{ route('admin.konten.update') }}" class="space-y-4">
        @csrf
        @if(session('contact_validation_failed') || $errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <p class="font-bold">Kontak belum tersimpan. Periksa field yang ditandai di bawah.</p>
        </div>
        @endif
        @php
            $kontakItems = [
                'alamat' => ['label' => 'Alamat Lengkap', 'icon' => 'fa-map-marker-alt', 'type' => 'text'],
                'telepon' => ['label' => 'Nomor Telepon', 'icon' => 'fa-phone', 'type' => 'text'],
                'email' => ['label' => 'Email Sekolah', 'icon' => 'fa-envelope', 'type' => 'email'],
                'wa' => ['label' => 'Nomor WhatsApp', 'icon' => 'fa-whatsapp', 'type' => 'tel', 'placeholder' => '081234567890'],
                'ig' => ['label' => 'Link Instagram', 'icon' => 'fa-instagram', 'type' => 'url', 'placeholder' => 'https://instagram.com/...'],
                'tiktok' => ['label' => 'Link TikTok', 'icon' => 'fa-music', 'type' => 'url', 'placeholder' => 'https://tiktok.com/@...'],
                'jam_op' => ['label' => 'Jam Operasional', 'icon' => 'fa-clock', 'type' => 'text', 'placeholder' => 'Senin - Jumat: 07.00 - 13.30 WIB'],
            ];
            $currentKonten = \App\Models\KontenWeb::all()->pluck('konten', 'tipe');
        @endphp

        @foreach($kontakItems as $key => $item)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <label class="md:col-span-1 text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas {{ $item['icon'] }} text-[var(--color-accent)] w-5"></i> {{ $item['label'] }}
            </label>
            <div class="md:col-span-3">
                <input type="{{ $item['type'] }}" name="konten_items[{{ $key }}]" 
                       value="{{ old("konten_items.$key", $currentKonten[$key] ?? '') }}"
                       placeholder="{{ $item['placeholder'] ?? '' }}"
                       class="w-full px-4 py-2 border-2 {{ $errors->has("konten_items.$key") ? 'border-red-500 bg-red-50' : 'border-gray-200' }} rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                @error("konten_items.$key")
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        @endforeach

        <input type="hidden" name="tipe" value="kontak_multi">
        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-[var(--color-primary)] text-white px-8 py-2.5 rounded-xl font-bold hover:bg-[var(--color-primary-light)] transition shadow-md">
                <i class="fas fa-save mr-2"></i> Simpan Semua Kontak
            </button>
        </div>
    </form>
</div>
@endif
@endsection
