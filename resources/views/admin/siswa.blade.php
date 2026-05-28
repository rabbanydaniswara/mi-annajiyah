@extends('layouts.admin')
@section('title', 'Kelola Siswa')
@section('header_icon', 'user-graduate')
@section('header_title', 'Manajemen Siswa')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold"><i class="fas fa-user-graduate mr-2"></i>Kelola Data Siswa</h3>
    <p class="text-green-200 mt-1">Kelola data siswa, filter per tahun & kelas.</p>
</div>

<div class="flex gap-3 mb-6">
    <a href="{{ route('admin.export', 'siswa') }}" class="bg-green-600 text-white px-5 py-2 rounded-xl font-semibold hover:bg-green-700 transition text-sm"><i class="fas fa-file-excel mr-1"></i> Export Excel</a>
    <a href="{{ route('admin.export', ['type' => 'siswa', 'format' => 'pdf']) }}" target="_blank" class="bg-red-600 text-white px-5 py-2 rounded-xl font-semibold hover:bg-red-700 transition text-sm"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-user-graduate text-white text-lg"></i></div>
        <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $totalSiswa }}</h3><p class="text-gray-500 text-xs">Total Siswa</p>
    </div>
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-layer-group text-white text-lg"></i></div>
        <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $totalKelas }}</h3><p class="text-gray-500 text-xs">Kelas</p>
    </div>
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-calendar text-white text-lg"></i></div>
        <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $totalTahun }}</h3><p class="text-gray-500 text-xs">Tahun Ajaran</p>
    </div>
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
        <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-clock text-white text-lg"></i></div>
        <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $totalPending }}</h3><p class="text-gray-500 text-xs">Pending</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-filter mr-2"></i>Filter & Cari</h3>
    <form method="GET" action="{{ route('admin.siswa') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="text-sm font-semibold text-[var(--color-primary)] block mb-1">Cari (nama, NISN, NIS, no WA, ortu)</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci..." class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
        </div>
        <div class="flex-1 min-w-[120px]">
            <label class="text-sm font-semibold text-[var(--color-primary)] block mb-1">Tahun</label>
            <select name="tahun" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                <option value="">Semua</option>
                @foreach($tahunList as $t)<option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[120px]">
            <label class="text-sm font-semibold text-[var(--color-primary)] block mb-1">Kelas</label>
            <select name="kelas" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                <option value="">Semua</option>
                @foreach($kelasList as $k)<option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>@endforeach
            </select>
        </div>
        <button type="submit" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2 rounded-xl font-semibold text-sm hover:bg-[var(--color-accent-dark)] transition"><i class="fas fa-search mr-1"></i> Cari</button>
        <a href="{{ route('admin.siswa') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl font-semibold text-sm hover:bg-gray-300 transition"><i class="fas fa-undo mr-1"></i> Reset</a>
    </form>
</div>

{{-- Group per Kelas --}}
<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-layer-group mr-2"></i>Per Kelas</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        @foreach($groupByKelas as $kelas => $list)
        <div class="bg-gray-50 rounded-xl p-4 text-center hover:-translate-y-1 transition">
            <p class="text-sm font-bold text-[var(--color-primary)]">Kelas {{ $kelas }}</p>
            <p class="text-2xl font-black text-[var(--color-accent)]">{{ $list->count() }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- Form --}}
<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
        <i class="fas {{ $edit ? 'fa-edit' : 'fa-plus-circle' }} mr-2"></i>{{ $edit ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
    </h3>
    <form method="POST" action="{{ route('admin.siswa.store') }}">
        @csrf
        @if($edit)<input type="hidden" name="id" value="{{ $edit->id }}">@endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-user mr-1"></i>Nama *</label><input type="text" name="nama" value="{{ $edit->nama ?? '' }}" required class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-id-card mr-1"></i>NISN</label><input type="text" name="nisn" value="{{ $edit->nisn ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-layer-group mr-1"></i>Kelas</label><input type="text" name="kelas" value="{{ $edit->kelas ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fab fa-whatsapp mr-1"></i>No WA</label><input type="text" name="no_wa" value="{{ $edit->no_wa ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-user-friends mr-1"></i>Nama Ortu</label><input type="text" name="nama_ortu" value="{{ $edit->nama_ortu ?? '' }}" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-map-marker-alt mr-1"></i>Alamat</label><textarea name="alamat" rows="2" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm resize-none">{{ $edit->alamat ?? '' }}</textarea></div>
        </div>
        <div class="mt-4 flex gap-3">
            <button type="submit" class="bg-[var(--color-primary)] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> {{ $edit ? 'Update' : 'Simpan' }}</button>
            @if($edit)<a href="{{ route('admin.siswa') }}" class="bg-gray-500 text-white px-6 py-2 rounded-xl font-semibold hover:bg-gray-600 transition text-sm">Batal</a>@endif
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl p-6 shadow-sm animate-fade overflow-x-auto">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-list mr-2"></i>Daftar Siswa</h3>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 text-[var(--color-primary)] uppercase text-xs font-semibold">
                <th class="p-3 text-left">Nama</th><th class="p-3 text-left">NISN</th><th class="p-3 text-left">Kelas</th><th class="p-3 text-left">No WA</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $s)
            <tr class="border-b border-gray-100 hover:bg-yellow-50/50 transition">
                <td class="p-3 font-semibold">{{ $s->nama }}</td>
                <td class="p-3">{{ $s->nisn ?: '-' }}</td>
                <td class="p-3"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs">{{ $s->kelas ?: '-' }}</span></td>
                <td class="p-3">{{ $s->no_wa ?: '-' }}</td>
                <td class="p-3">
                    @if($s->status_ppdb === 'diterima')<span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-bold">Diterima</span>
                    @elseif($s->status_ppdb === 'ditolak')<span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-xs font-bold">Ditolak</span>
                    @else<span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs font-bold">Pending</span>
                    @endif
                </td>
                <td class="p-3 flex gap-1">
                    <a href="{{ route('admin.siswa', ['edit' => $s->id]) }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-2 py-1 rounded text-xs font-semibold"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{ route('admin.siswa.destroy', $s->id) }}" class="inline" 
                          data-confirm="Yakin ingin menghapus siswa {{ $s->nama }}?"
                          data-title="Konfirmasi Hapus"
                          data-button="Hapus"
                          data-type="danger">
@csrf @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-16">
                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-graduate text-2xl text-green-300"></i>
                    </div>
                    <p class="text-gray-400 font-medium mb-1">Tidak ada data siswa.</p>
                    <p class="text-gray-300 text-sm">Data siswa akan muncul setelah pendaftaran PPDB diterima.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $siswa->links() }}
    </div>
</div>
@endsection
