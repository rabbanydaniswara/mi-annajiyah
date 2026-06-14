@extends('layouts.admin')
@section('title', 'Kelola Jadwal')
@section('header_icon', 'calendar-alt')
@section('header_title', 'Manajemen Jadwal')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold"><i class="fas fa-calendar-alt mr-2"></i>Kelola Jadwal Pembelajaran</h3>
    <p class="text-green-200 mt-1">Atur jadwal pelajaran, jam mengajar, dan alokasi guru.</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-1 hover:shadow-lg animate-fade">
        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-2"><i class="fas fa-calendar-alt text-sm"></i></div>
        <h3 class="text-xl font-black text-[var(--color-primary)]">{{ $totalJadwal }}</h3><p class="text-gray-400 text-[10px] uppercase font-bold tracking-wider">Total Jadwal</p>
    </div>
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-1 hover:shadow-lg animate-fade">
        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mb-2"><i class="fas fa-chalkboard-user text-sm"></i></div>
        <h3 class="text-xl font-black text-[var(--color-primary)]">{{ $totalGuru }}</h3><p class="text-gray-400 text-[10px] uppercase font-bold tracking-wider">Guru Aktif</p>
    </div>
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-1 hover:shadow-lg animate-fade">
        <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-2"><i class="fas fa-layer-group text-sm"></i></div>
        <h3 class="text-xl font-black text-[var(--color-primary)]">{{ $totalKelas }}</h3><p class="text-gray-400 text-[10px] uppercase font-bold tracking-wider">Kelas</p>
    </div>
    <div class="flex items-center justify-center">
        <a href="{{ route('admin.jadwal.print') }}" target="_blank" rel="noopener noreferrer" class="w-full h-full bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] text-[var(--color-primary)] rounded-2xl font-black shadow-lg shadow-yellow-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 border-b-4 border-yellow-600">
            <i class="fas fa-print text-xl"></i>
            <div class="text-left">
                <p class="text-[10px] uppercase tracking-widest leading-none mb-1">Cetak Mode</p>
                <p class="text-xs font-black">PRINT JADWAL</p>
            </div>
        </a>
    </div>
</div>

{{-- Form --}}
<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade">
    <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
        <i class="fas {{ $edit ? 'fa-edit' : 'fa-plus-circle' }} mr-2"></i>{{ $edit ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}
    </h3>
    <form method="POST" action="{{ route('admin.jadwal.store') }}">
        @csrf
        @if($edit)<input type="hidden" name="id" value="{{ $edit->id }}">@endif
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-calendar-day mr-1"></i>Hari</label>
                <select name="hari" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" {{ $edit && $edit->hari === $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-hourglass-start mr-1"></i>Jam Mulai</label><input type="time" name="jam_mulai" value="{{ $edit->jam_mulai ?? '' }}" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-hourglass-end mr-1"></i>Jam Selesai</label><input type="time" name="jam_selesai" value="{{ $edit->jam_selesai ?? '' }}" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-book mr-1"></i>Mapel</label><input type="text" name="mapel" value="{{ $edit->mapel ?? '' }}" placeholder="Matematika" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div>
                <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-chalkboard-user mr-1"></i>Guru</label>
                <select name="id_guru" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                    <option value="">Pilih Guru</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id }}" {{ $edit && $edit->id_guru == $g->id ? 'selected' : '' }}>{{ $g->nama }} - {{ $g->mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-users mr-1"></i>Kelas</label><input type="text" name="kelas" value="{{ $edit->kelas ?? '' }}" placeholder="3A" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
            <div><label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-door-open mr-1"></i>Ruangan</label><input type="text" name="ruangan" value="{{ $edit->ruangan ?? '' }}" placeholder="Ruang 1" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm"></div>
        </div>
        <div class="mt-4 flex gap-3">
            <button type="submit" class="bg-[var(--color-primary)] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> {{ $edit ? 'Update' : 'Simpan' }}</button>
            @if($edit)<a href="{{ route('admin.jadwal') }}" class="bg-gray-500 text-white px-6 py-2 rounded-xl font-semibold hover:bg-gray-600 transition text-sm">Batal</a>@endif
        </div>
    </form>
</div>

{{-- Search & Filter --}}
<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade" 
     x-data="{ 
        activeTab: '{{ now()->translatedFormat('l') }}', 
        searchQuery: '', 
        classFilter: 'Semua', 
        viewMode: 'grid',
        getMapelColor(mapel) {
            const m = mapel.toLowerCase();
            if(m.includes('fiqih') || m.includes('akidah') || m.includes('qur') || m.includes('ski') || m.includes('arab')) return 'border-l-green-500 bg-green-50/50 text-green-700';
            if(m.includes('matematika') || m.includes('ipa') || m.includes('indonesia') || m.includes('inggris')) return 'border-l-blue-500 bg-blue-50/50 text-blue-700';
            if(m.includes('pjok') || m.includes('olahraga')) return 'border-l-orange-500 bg-orange-50/50 text-orange-700';
            if(m.includes('seni') || m.includes('prakarya')) return 'border-l-purple-500 bg-purple-50/50 text-purple-700';
            return 'border-l-gray-400 bg-gray-50/50 text-gray-700';
        }
     }">
    
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
        <div class="flex items-center gap-4">
            <h3 class="text-lg font-bold text-[var(--color-primary)] border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas fa-list mr-2"></i>Daftar Jadwal
            </h3>
            {{-- View Switcher --}}
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <button @@click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white shadow-sm text-[var(--color-primary)]' : 'text-gray-400'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-table"></i> Tabel
                </button>
                <button @@click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow-sm text-[var(--color-primary)]' : 'text-gray-400'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-th-large"></i> Grid
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 w-full lg:w-auto">
            <div class="relative flex-1 lg:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari Mapel atau Guru..." class="w-full pl-9 pr-4 py-2 border-2 border-gray-100 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
            </div>
            <select x-model="classFilter" class="px-4 py-2 border-2 border-gray-100 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm" aria-label="Filter kelas jadwal">
                <option value="Semua">Semua Kelas</option>
                @php
                    $classes = $jadwal->pluck('kelas')->unique()->sort();
                @endphp
                @foreach($classes as $c)
                <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex overflow-x-auto gap-2 pb-6 no-scrollbar">
        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
        <button @@click="activeTab = '{{ $h }}'" 
                :class="activeTab === '{{ $h }}' ? 'bg-[var(--color-primary)] text-white scale-105 shadow-md' : 'bg-gray-100 text-gray-400 hover:bg-gray-200'"
                class="px-6 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap uppercase tracking-widest">
            {{ $h }}
        </button>
        @endforeach
    </div>

    {{-- TABLE MODE --}}
    <div x-show="viewMode === 'table'" class="overflow-x-auto" x-transition>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-[var(--color-primary)] uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                    <th class="p-4 text-left">Waktu</th>
                    <th class="p-4 text-left">Mata Pelajaran</th>
                    <th class="p-4 text-left">Guru Pengajar</th>
                    <th class="p-4 text-left">Kelas</th>
                    <th class="p-4 text-left">Ruangan</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($jadwal as $j)
                <tr x-show="(activeTab === '{{ $j->hari }}') && 
                            (classFilter === 'Semua' || classFilter === '{{ $j->kelas }}') &&
                            ({{ json_encode(strtolower($j->mapel . ' ' . ($j->guru?->nama ?? ''))) }}.includes(searchQuery.toLowerCase()))"
                    class="hover:bg-yellow-50/30 transition group">
                    <td class="p-4">
                        <div class="flex items-center gap-2 text-gray-500 text-xs">
                            <i class="fas fa-clock text-[var(--color-accent)] opacity-50"></i>
                            <span class="font-mono bg-gray-50 px-2 py-0.5 rounded border">{{ substr($j->jam_mulai,0,5) }}</span>
                            <span>-</span>
                            <span class="font-mono bg-gray-50 px-2 py-0.5 rounded border">{{ substr($j->jam_selesai,0,5) }}</span>
                        </div>
                    </td>
                    <td class="p-4 font-bold text-gray-800">{{ $j->mapel }}</td>
                    <td class="p-4 text-gray-600 font-medium">
                        <div class="flex items-center gap-2">
                            @if($j->guru?->foto)
                                <img src="{{ asset(\App\Helpers\ImageHelper::getThumbnail($j->guru->foto)) }}" class="w-6 h-6 rounded-full object-cover" loading="lazy" decoding="async" alt="">
                            @else
                                <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-[10px]"><i class="fas fa-user"></i></div>
                            @endif
                            {{ $j->guru?->nama ?? '-' }}
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border border-indigo-100">{{ $j->kelas }}</span>
                    </td>
                    <td class="p-4 text-xs text-gray-500 font-bold uppercase tracking-tight">
                        <i class="fas fa-door-open mr-1 opacity-40"></i> {{ $j->ruangan ?? '-' }}
                    </td>
                    <td class="p-4">
                        <div class="flex gap-2 justify-center">
                            <a href="{{ route('admin.jadwal', ['edit' => $j->id]) }}" class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                            <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id) }}" class="inline" 
                                  data-confirm="Yakin ingin menghapus jadwal {{ $j->mapel }} ({{ $j->kelas }})?"
                                  data-title="Konfirmasi Hapus"
                                  data-button="Hapus"
                                  data-type="danger">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- GRID MODE (Visual Board) --}}
    <div x-show="viewMode === 'grid'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($classes as $c)
            <div x-show="classFilter === 'Semua' || classFilter === '{{ $c }}'" 
                 class="bg-gray-50/50 rounded-3xl border border-gray-100 p-5 flex flex-col h-full animate-fade">
                <div class="flex justify-between items-center mb-5 border-b border-gray-200/50 pb-3">
                    <h4 class="text-sm font-black text-[var(--color-primary)] flex items-center gap-2">
                        <div class="w-8 h-8 bg-[var(--color-primary)] text-white rounded-lg flex items-center justify-center text-xs shadow-md shadow-green-900/20">{{ $c }}</div>
                        KELAS {{ $c }}
                    </h4>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest bg-white px-2 py-1 rounded-lg border">MI Annajiyah</span>
                </div>
                
                <div class="space-y-3 flex-1">
                    @php
                        $daySchedules = $jadwal->where('kelas', $c);
                    @endphp
                    @foreach($daySchedules as $j)
                    <div x-show="activeTab === '{{ $j->hari }}' && ({{ json_encode(strtolower($j->mapel . ' ' . ($j->guru?->nama ?? ''))) }}.includes(searchQuery.toLowerCase()))"
                         class="p-4 rounded-2xl border-l-4 shadow-sm hover:shadow-md transition-all group relative overflow-hidden"
                         :class="getMapelColor({{ json_encode($j->mapel) }})">
                        
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-tighter opacity-70 mb-1">
                                    {{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}
                                </p>
                                <h5 class="font-black text-sm leading-tight mb-2 text-gray-900 group-hover:text-[var(--color-primary)] transition-colors">{{ $j->mapel }}</h5>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-5 h-5 bg-white/80 rounded-full flex items-center justify-center text-[8px] border"><i class="fas fa-user"></i></div>
                                    <span class="text-[10px] font-bold text-gray-600">{{ $j->guru?->nama ?? '-' }}</span>
                                </div>
                            </div>
                            
                            {{-- Actions in Grid --}}
                            <div class="flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.jadwal', ['edit' => $j->id]) }}" class="w-7 h-7 bg-white text-blue-600 rounded-lg flex items-center justify-center shadow-sm hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-edit text-[10px]"></i></a>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id) }}" class="inline" 
                                      data-confirm="Yakin ingin menghapus jadwal {{ $j->mapel }}?"
                                      data-title="Konfirmasi Hapus"
                                      data-button="Hapus"
                                      data-type="danger">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 bg-white text-red-600 rounded-lg flex items-center justify-center shadow-sm hover:bg-red-600 hover:text-white transition-all"><i class="fas fa-trash text-[10px]"></i></button>
                                </form>
                            </div>
                        </div>
                        
                        {{-- Background Icon --}}
                        <i class="fas fa-book-open absolute right-[-10px] bottom-[-10px] text-4xl opacity-5 rotate-12 group-hover:scale-125 transition-transform duration-500"></i>
                    </div>
                    @endforeach

                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($jadwal->isEmpty())
    <div class="text-center py-20">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-calendar-alt text-3xl text-blue-300"></i>
        </div>
        <p class="text-gray-400 font-medium mb-1">Belum ada jadwal.</p>
        <p class="text-gray-300 text-sm mb-5">Atur jadwal pelajaran untuk setiap hari dan kelas.</p>
        <button onclick="document.querySelector('select[name=hari]').focus()" class="bg-[var(--color-primary)] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[var(--color-primary-light)] transition shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Jadwal Pertama
        </button>
    </div>
    @endif
</div>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
[x-cloak] { display: none !important; }
</style>
@endsection
