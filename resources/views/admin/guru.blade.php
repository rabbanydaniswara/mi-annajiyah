@extends('layouts.admin')
@section('title', 'Kelola Guru')
@section('header_icon', 'chalkboard-user')
@section('header_title', 'Manajemen Tenaga Pendidik')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold"><i class="fas fa-chalkboard-user mr-2"></i>Kelola Tenaga Pendidik</h3>
    <p class="text-green-200 mt-1">Total: {{ $totalGuru }} guru | {{ $totalMapel }} mata pelajaran/jabatan</p>
</div>

<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-[var(--color-primary)] text-white px-5 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <a href="{{ route('admin.export', 'guru') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2 rounded-xl font-semibold hover:bg-green-700 transition text-sm">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
    <a href="{{ route('admin.export', ['type' => 'guru', 'format' => 'pdf']) }}" target="_blank" class="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2 rounded-xl font-semibold hover:bg-red-700 transition text-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade sticky top-6">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas fa-{{ $edit ? 'edit' : 'user-plus' }} mr-2"></i>{{ $edit ? 'Edit Guru' : 'Tambah Guru' }}
            </h3>
            <form method="POST" action="{{ route('admin.guru.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @if($edit)<input type="hidden" name="id" value="{{ $edit->id }}">@endif

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama" value="{{ $edit->nama ?? '' }}" required
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm" placeholder="Nama lengkap guru">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ $edit->jabatan ?? '' }}"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm" placeholder="Wali Kelas 1 / Guru PAI">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Mata Pelajaran *</label>
                    <input type="text" name="mapel" value="{{ $edit->mapel ?? '' }}" required
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm" placeholder="Mata pelajaran yang diajarkan">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ $edit->nip ?? '' }}"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm" placeholder="Nomor Induk Pegawai">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">No. Telepon</label>
                    <input type="text" name="no_telp" value="{{ $edit->no_telp ?? '' }}"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm" placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan Tampil</label>
                    <input type="number" name="urutan" value="{{ $edit->urutan ?? 0 }}" min="0"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Foto</label>
                    <input type="file" name="foto" accept="image/*"
                           class="w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[var(--color-accent)] file:text-[var(--color-primary)] file:font-semibold file:cursor-pointer">
                    @if($edit && $edit->foto)
                    <img src="{{ asset($edit->foto) }}" class="w-16 h-16 rounded-full object-cover mt-2 border-2 border-[var(--color-accent)]">
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="tampilkan" id="tampilkan" value="1" {{ (!$edit || $edit->tampilkan) ? 'checked' : '' }} class="w-4 h-4 rounded accent-[var(--color-accent)]">
                    <label for="tampilkan" class="text-xs font-semibold text-gray-700">Tampilkan di halaman publik</label>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-[var(--color-primary)] text-white py-2.5 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm">
                        <i class="fas fa-save mr-1"></i> {{ $edit ? 'Update' : 'Simpan' }}
                    </button>
                    @if($edit)
                    <a href="{{ route('admin.guru') }}" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition text-sm">Batal</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Guru --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas fa-users mr-2"></i>Daftar Guru ({{ $totalGuru }})
            </h3>
            <form method="GET" action="{{ route('admin.guru') }}" class="flex gap-2 mb-4">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, mapel, jabatan, NIP..." class="w-full pl-9 pr-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
                </div>
                <button type="submit" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-4 py-2 rounded-xl font-semibold text-sm"><i class="fas fa-search"></i></button>
                @if(request('q'))<a href="{{ route('admin.guru') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm"><i class="fas fa-times"></i></a>@endif
            </form>
            @if($guru->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chalkboard-teacher text-2xl text-green-300"></i>
                </div>
                <p class="text-gray-400 font-medium mb-2">Belum ada data guru.</p>
                <p class="text-gray-300 text-sm mb-4">Tambahkan guru pertama di form sebelah kiri.</p>
                <button onclick="document.querySelector('input[name=nama]').focus()" class="text-[var(--color-primary)] text-sm font-semibold hover:underline">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Guru Pertama
                </button>
            </div>
            @else
            <div class="space-y-3">
                @foreach($guru as $g)
                <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                    @if($g->foto)
                    @php 
                        $thumbPath = \App\Helpers\ImageHelper::getThumbnail($g->foto);
                        $version = file_exists(public_path($thumbPath)) ? filemtime(public_path($thumbPath)) : time();
                    @endphp
                    <img src="{{ asset($thumbPath) }}?v={{ $version }}" alt="{{ $g->nama }}" class="w-14 h-14 rounded-full object-cover border-3 border-[var(--color-accent)] shrink-0">
                    @else
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center shrink-0 border-2 border-[var(--color-accent)]">
                        <i class="fas fa-user text-green-400 text-xl"></i>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <strong class="text-[var(--color-primary)] text-sm">{{ $g->nama }}</strong>
                            @if(!$g->tampilkan)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-500">Disembunyikan</span>
                            @endif
                        </div>
                        <p class="text-[var(--color-accent)] text-xs font-semibold mt-0.5">{{ $g->jabatan ?? '-' }}</p>
                        <p class="text-gray-400 text-xs">{{ $g->mapel }}</p>
                    </div>
                    <div class="flex gap-1 shrink-0">
                        <a href="{{ route('admin.guru', ['edit' => $g->id]) }}" title="Edit"
                           class="w-8 h-8 bg-yellow-100 text-yellow-600 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.guru.destroy', $g->id) }}" class="inline" 
                              data-confirm="Yakin ingin menghapus guru {{ $g->nama }}?"
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
            <div class="mt-4">
                {{ $guru->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
