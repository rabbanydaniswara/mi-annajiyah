@extends('layouts.admin')
@section('title', 'Kelola PPDB')
@section('header_icon', 'users')
@section('header_title', 'Kelola PPDB')

@section('content')
<div x-data="{ 
        modalOpen: false, 
        selected: {}, 
        detailStatus: 'pending',
        detailNote: '',
        documentRoute: '{{ route('admin.ppdb.document', ['siswa' => '__ID__', 'field' => '__FIELD__']) }}',
        filesMap: {
            'file_akte': 'Akte Kelahiran',
            'file_kk': 'Kartu Keluarga',
            'file_ktp_ortu': 'KTP Orang Tua',
            'file_ijazah': 'Ijazah Terakhir'
        },
        isImage(path) {
            if(!path) return false;
            const ext = path.split('.').pop().toLowerCase();
            return ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
        },
        formatDate(dateStr) {
            if(!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        },
        getStatusColor(status) {
            if(status === 'diterima') return 'text-green-600';
            if(status === 'daftar_ulang') return 'text-green-600';
            if(status === 'ditolak') return 'text-red-600';
            if(status === 'diverifikasi') return 'text-blue-600';
            if(status === 'berkas_kurang') return 'text-orange-600';
            return 'text-yellow-600';
        },
        getDocumentUrl(field) {
            if(!this.selected.id || !field) return '';
            return this.documentRoute.replace('__ID__', this.selected.id).replace('__FIELD__', field);
        },
        openDetail(p) {
            this.selected = JSON.parse(JSON.stringify(p));
            // Fix Carbon date objects if they exist
            if (this.selected.tanggal_lahir && typeof this.selected.tanggal_lahir === 'object') {
                this.selected.tanggal_lahir = this.selected.tanggal_lahir.date;
            }
            this.detailStatus = this.selected.status_ppdb || 'pending';
            this.detailNote = this.selected.catatan_verifikasi || '';
            this.modalOpen = true;
        }
     }" class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-3xl p-8 text-white relative overflow-hidden shadow-xl animate-fade">
        <div class="absolute top-[-20%] right-[-10%] w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h3 class="text-2xl font-black mb-1"><i class="fas fa-users-cog mr-2"></i>Kelola Pendaftaran Siswa</h3>
            <p class="text-green-200 opacity-80 text-sm">Verifikasi dokumen dan kelola status pendaftaran peserta didik baru.</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 animate-slide">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</p>
                <p class="text-xl font-black text-[var(--color-primary)]">{{ $totalSemua }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 animate-slide" style="animation-delay: 0.1s">
            <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-clock"></i></div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending</p>
                <p class="text-xl font-black text-yellow-600">{{ $totalPending }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 animate-slide" style="animation-delay: 0.2s">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-check-circle"></i></div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Diterima</p>
                <p class="text-xl font-black text-green-600">{{ $totalDiterima }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 animate-slide" style="animation-delay: 0.3s">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl"><i class="fas fa-times-circle"></i></div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ditolak</p>
                <p class="text-xl font-black text-red-600">{{ $totalDitolak }}</p>
            </div>
        </div>
    </div>

    {{-- Actions + Search --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.ppdb') }}" class="flex flex-wrap gap-2 items-center">
            <div class="flex-1 min-w-[200px] relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor, nama, NISN, NIS, no WA, ortu..." class="w-full pl-9 pr-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
            </div>
            <select name="tahun_ajaran" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
                <option value="">Semua Tahun</option>
                @foreach($tahunAjaranList as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun_ajaran') === $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
            </select>
            <select name="kelas" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas }}" {{ request('kelas') === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm" title="Tanggal daftar dari">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm" title="Tanggal daftar sampai">
            <button type="submit" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[var(--color-accent-dark)] transition"><i class="fas fa-search mr-1"></i> Cari</button>
            <a href="{{ route('admin.ppdb') }}" class="bg-gray-100 text-gray-600 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition"><i class="fas fa-undo mr-1"></i> Reset</a>
            <a href="{{ route('admin.export', array_filter(['type' => 'ppdb', 'tahun_ajaran' => request('tahun_ajaran'), 'kelas' => request('kelas'), 'status' => request('status'), 'tanggal_dari' => request('tanggal_dari'), 'tanggal_sampai' => request('tanggal_sampai')])) }}" class="bg-green-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-green-700 transition shadow-lg shadow-green-600/20 text-sm flex items-center gap-2 ml-auto">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </form>
        <form id="bulkStatusForm" method="POST" action="{{ route('admin.ppdb.bulkUpdateStatus') }}" class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2 items-center">
            @csrf
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest mr-2">Bulk Action</span>
            <select name="status" required class="px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
                @foreach($statusOptions as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="catatan_verifikasi" placeholder="Catatan opsional untuk data terpilih" class="flex-1 min-w-[220px] px-3 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none text-sm">
            <button type="submit" class="bg-[var(--color-primary)] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[var(--color-primary-light)] transition">
                <i class="fas fa-layer-group mr-1"></i> Update Terpilih
            </button>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-fade">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-[var(--color-primary)] uppercase tracking-wider text-sm">
                <i class="fas fa-list mr-2 text-[var(--color-accent)]"></i>Daftar Pendaftar
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/50 text-[var(--color-primary)] uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                        <th class="p-4 text-left w-10"></th>
                        <th class="p-4 text-left">No. Daftar</th>
                        <th class="p-4 text-left">Nama Lengkap</th>
                        <th class="p-4 text-left">Identitas (NISN/NIS)</th>
                        <th class="p-4 text-left">Kontak WA</th>
                        <th class="p-4 text-left">Tgl Daftar</th>
                        <th class="p-4 text-left">Status</th>
                        <th class="p-4 text-left">Dokumen</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pendaftar as $p)
                    <tr class="hover:bg-yellow-50/30 transition group">
                        <td class="p-4">
                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" form="bulkStatusForm" class="w-4 h-4 rounded accent-[var(--color-accent)]">
                        </td>
                        <td class="p-4">
                            <div class="font-mono font-black text-[var(--color-primary)] text-xs">{{ $p->nomor_pendaftaran ?: '-' }}</div>
                            <div class="text-[10px] text-gray-400 font-bold mt-1">{{ $p->tahun_ajaran ?: '-' }}</div>
                        </td>
                        <td class="p-4">
                            <a href="javascript:void(0)" @@click="openDetail(@js($p))" class="font-bold text-[var(--color-primary)] hover:text-[var(--color-accent)] transition text-left cursor-pointer outline-none block group-hover:translate-x-1 duration-300">
                                {{ $p->nama }}
                                <i class="fas fa-chevron-right text-[8px] ml-1 opacity-0 group-hover:opacity-100 transition-all"></i>
                            </a>
                            <p class="text-[10px] text-gray-400 mt-1 italic">{{ $p->asal_sekolah ?: 'TK/PAUD Belum Diisi' }}</p>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[10px] text-gray-400 font-bold uppercase">NISN: {{ $p->nisn ?: '-' }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">NIS: {{ $p->nis ?: '-' }}</span>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-green-600 text-xs">
                            <i class="fab fa-whatsapp mr-1"></i> {{ $p->no_wa }}
                        </td>
                        <td class="p-4 text-[10px] text-gray-500 font-medium">{{ $p->tanggal_daftar?->format('d M Y, H:i') }}</td>
                        <td class="p-4">
                            @php
                                $statusClass = match (\App\Helpers\PpdbHelper::statusTone($p->status_ppdb)) {
                                    'green' => 'bg-green-100 text-green-700',
                                    'red' => 'bg-red-100 text-red-700',
                                    'blue' => 'bg-blue-100 text-blue-700',
                                    'orange' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <span class="{{ $statusClass }} px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter">{{ \App\Helpers\PpdbHelper::statusLabel($p->status_ppdb) }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex gap-1.5">
                                @foreach(['file_akte' => 'Akte', 'file_kk' => 'KK', 'file_ktp_ortu' => 'KTP', 'file_ijazah' => 'Ijazah'] as $field => $label)
                                    @if($p->$field)
                                    <div class="group/file relative">
                                        <a href="{{ route('admin.ppdb.document', ['siswa' => $p->id, 'field' => $field]) }}" target="_blank" rel="noopener noreferrer" class="block w-7 h-7 rounded-lg border border-gray-100 overflow-hidden hover:border-[var(--color-accent)] transition shadow-sm hover:scale-110 duration-200">
                                            @php $ext = pathinfo($p->$field, PATHINFO_EXTENSION); @endphp
                                            @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                                <img src="{{ route('admin.ppdb.document', ['siswa' => $p->id, 'field' => $field]) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-red-50 flex items-center justify-center text-[8px] text-red-500 font-black">PDF</div>
                                            @endif
                                        </a>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex gap-1.5 justify-center">
                                <form method="POST" action="{{ route('admin.ppdb.updateStatus') }}" class="inline" 
                                      data-confirm="Tandai pendaftar {{ $p->nama }} sudah diverifikasi?"
                                      data-title="Konfirmasi Verifikasi"
                                      data-button="Verifikasi"
                                      data-type="success"
                                      data-icon="fa-check-circle">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $p->id }}">
                                    <input type="hidden" name="status" value="diverifikasi">
                                    <button type="submit" class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Verifikasi"><i class="fas fa-clipboard-check text-xs"></i></button>
                                </form>
                                <form method="POST" action="{{ route('admin.ppdb.updateStatus') }}" class="inline" 
                                      data-confirm="Tandai berkas {{ $p->nama }} masih kurang?"
                                      data-title="Konfirmasi Berkas Kurang"
                                      data-button="Tandai"
                                      data-type="warning"
                                      data-icon="fa-exclamation-circle">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $p->id }}">
                                    <input type="hidden" name="status" value="berkas_kurang">
                                    <button type="submit" class="w-8 h-8 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center hover:bg-orange-600 hover:text-white transition-all shadow-sm" title="Berkas Kurang"><i class="fas fa-exclamation text-xs"></i></button>
                                </form>
                                <form method="POST" action="{{ route('admin.ppdb.destroy', $p->id) }}" class="inline" 
                                      data-confirm="Yakin ingin menghapus pendaftar {{ $p->nama }}?"
                                      data-title="Konfirmasi Hapus"
                                      data-button="Hapus"
                                      data-type="danger">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-gray-50 text-gray-400 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-20">
                            <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-5">
                                <i class="fas fa-inbox text-3xl text-green-300"></i>
                            </div>
                            <p class="text-gray-400 font-medium mb-1">Belum ada pendaftar yang masuk.</p>
                            <p class="text-gray-300 text-sm">Pendaftar akan muncul setelah calon siswa mengisi formulir PPDB.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-50">
            {{ $pendaftar->links() }}
        </div>
    </div>

    {{-- MODAL AREA (Truly outside the table card) --}}
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[1000] flex items-center justify-center p-4 md:p-10 bg-black/70 backdrop-blur-md" 
         style="display: none;"
         @@keydown.escape.window="modalOpen = false">
        
        <div class="bg-white w-full max-w-5xl max-h-[90vh] rounded-[2.5rem] shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)] overflow-hidden flex flex-col relative animate-scale" @@click.away="if(!deleteModal) modalOpen = false">
            {{-- Header --}}
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] text-white rounded-3xl flex items-center justify-center text-2xl shadow-xl rotate-3">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h4 class="text-2xl font-black text-[var(--color-primary)] leading-tight tracking-tight" x-text="selected.nama"></h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-gray-100 text-gray-500" x-text="selected.nomor_pendaftaran || '-'"></span>
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-blue-50 text-blue-600" x-text="selected.tahun_ajaran || '-'"></span>
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded" :class="getStatusColor(selected.status_ppdb).replace('text', 'bg').replace('600', '100') + ' ' + getStatusColor(selected.status_ppdb)" x-text="selected.status_ppdb"></span>
                        </div>
                    </div>
                </div>
                <button @@click="modalOpen = false" class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white hover:rotate-90 transition-all duration-300 flex items-center justify-center shadow-inner">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto p-8 md:p-12 custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    {{-- Biodata --}}
                    <div class="space-y-8">
                        <div>
                            <h5 class="text-xs font-black text-[var(--color-accent)] uppercase tracking-[0.2em] mb-6 border-b border-gray-100 pb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Data Personal
                            </h5>
                            <div class="grid grid-cols-2 gap-y-8 gap-x-6">
                                <div class="col-span-2 sm:col-span-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Tempat, Tgl Lahir</p>
                                    <p class="font-bold text-gray-800 text-sm" x-text="(selected.tempat_lahir || '-') + ', ' + (formatDate(selected.tanggal_lahir))"></p>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Jenis Kelamin</p>
                                    <p class="font-bold text-gray-800 text-sm" x-text="selected.jenis_kelamin || '-'"></p>
                                </div>
                                <div class="col-span-2 sm:col-span-1 border-l-4 border-blue-500 pl-3 py-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">NISN</p>
                                    <p class="font-black text-blue-600 text-lg tracking-wider" x-text="selected.nisn || '-'"></p>
                                </div>
                                <div class="col-span-2 sm:col-span-1 border-l-4 border-indigo-500 pl-3 py-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">NIS</p>
                                    <p class="font-black text-indigo-600 text-lg tracking-wider" x-text="selected.nis || '-'"></p>
                                </div>
                                <div class="col-span-2 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Asal Sekolah (TK/PAUD)</p>
                                    <p class="font-bold text-gray-800" x-text="selected.asal_sekolah || '-'"></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Alamat Tinggal</p>
                                    <p class="text-sm text-gray-600 font-medium leading-relaxed" x-text="selected.alamat"></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Catatan Verifikasi Internal</p>
                                    <p class="text-sm text-gray-600 font-medium leading-relaxed" x-text="selected.catatan_verifikasi || 'Belum ada catatan.'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Wali & Dokumen --}}
                    <div class="space-y-8">
                        <div>
                            <h5 class="text-xs font-black text-[var(--color-accent)] uppercase tracking-[0.2em] mb-6 border-b border-gray-100 pb-3 flex items-center gap-2">
                                <i class="fas fa-file-invoice"></i> Orang Tua & Dokumen
                            </h5>
                            <div class="mb-10 p-5 rounded-3xl bg-green-50 border border-green-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-green-700/50 uppercase mb-1">Nama Orang Tua/Wali</p>
                                    <p class="text-lg font-black text-green-900" x-text="selected.nama_ortu"></p>
                                </div>
                                <a :href="'https://wa.me/' + (selected.no_wa ? selected.no_wa.replace(/\D/g,'') : '')" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-green-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30 hover:scale-110 transition-transform">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <template x-for="(label, field) in filesMap" :key="field">
                                    <div x-show="selected[field]" class="group/doc bg-white rounded-3xl p-3 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-3">
                                        <div class="flex justify-between items-center px-1">
                                            <p class="text-[9px] font-black text-gray-400 uppercase" x-text="label"></p>
                                            <i class="fas fa-check-circle text-green-400 text-[10px]"></i>
                                        </div>
                                        <a :href="getDocumentUrl(field)" target="_blank" rel="noopener noreferrer" class="relative block aspect-[4/3] rounded-2xl overflow-hidden border border-gray-50 bg-gray-50">
                                            <template x-if="isImage(selected[field])">
                                                <img :src="getDocumentUrl(field)" class="w-full h-full object-cover group-hover/doc:scale-110 transition duration-700">
                                            </template>
                                            <template x-if="!isImage(selected[field])">
                                                <div class="w-full h-full flex flex-col items-center justify-center text-red-500 gap-2">
                                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-file-pdf text-xl"></i></div>
                                                    <span class="text-[10px] font-black tracking-widest uppercase">LIHAT PDF</span>
                                                </div>
                                            </template>
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/doc:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[var(--color-primary)] shadow-xl translate-y-4 group-hover/doc:translate-y-0 transition-transform duration-300">
                                                    <i class="fas fa-external-link-alt text-sm"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="p-8 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-4 shrink-0">
                <form method="POST" action="{{ route('admin.ppdb.updateStatus') }}" class="w-full flex flex-col md:flex-row gap-3 items-stretch md:items-end">
                    @csrf
                    <input type="hidden" name="id" :value="selected.id">
                    <div class="md:w-56">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status</label>
                        <select name="status" x-model="detailStatus" class="w-full px-3 py-3 border-2 border-gray-200 rounded-2xl focus:border-[var(--color-accent)] outline-none text-sm font-bold">
                            @foreach($statusOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Catatan Internal</label>
                        <textarea name="catatan_verifikasi" x-model="detailNote" rows="2" class="w-full px-3 py-3 border-2 border-gray-200 rounded-2xl focus:border-[var(--color-accent)] outline-none text-sm resize-none" placeholder="Catatan hanya terlihat di admin"></textarea>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-[var(--color-primary)] text-white rounded-2xl font-black hover:bg-[var(--color-primary-light)] transition shadow-xl shadow-green-900/20 text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
