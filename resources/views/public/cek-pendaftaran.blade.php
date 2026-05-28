@extends('layouts.public')
@section('title', 'Cek Status Pendaftaran - MI Annajiyah')

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] pt-32 pb-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-[var(--color-accent)]/20 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-4 border border-[var(--color-accent)]/30">MONITORING</span>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-3">Cek Status Pendaftaran</h1>
        <p class="text-green-200 text-lg max-w-2xl mx-auto">Pantau status pendaftaran calon peserta didik secara real-time</p>
    </div>
</div>

<section class="py-16 bg-gray-50 min-h-[60vh]">
    <div class="max-w-3xl mx-auto px-4">
        {{-- Search Card --}}
        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-gray-100 -mt-24 relative z-20 fade-up">
            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-[var(--color-primary)]">Masukkan Data Pencarian</h3>
                <p class="text-gray-400 text-sm mt-1">Gunakan NISN, NIS, atau Nomor WhatsApp yang terdaftar</p>
            </div>
            
            <form method="GET" action="{{ route('cek-pendaftaran') }}" class="relative group">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[var(--color-accent)] transition-colors">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" name="q" value="{{ $cari }}" required
                               class="w-full pl-11 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white focus:ring-4 focus:ring-[var(--color-accent)]/10 outline-none transition-all text-sm font-medium"
                               placeholder="Contoh: 0012345678 atau 0812345678">
                    </div>
                    <button type="submit" class="bg-[var(--color-primary)] text-white px-8 py-4 rounded-2xl font-bold hover:bg-[var(--color-primary-light)] transition shadow-lg shadow-[var(--color-primary)]/20 active:scale-95 flex items-center justify-center gap-2">
                        <span>Cari Data</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Result Section --}}
        @if(!empty($cari))
            <div class="mt-12">
                @if($hasil)
                @php
                    $namaPublik = collect(preg_split('/\s+/', trim($hasil->nama)))
                        ->filter()
                        ->map(function ($bagian) {
                            $panjang = mb_strlen($bagian);

                            if ($panjang <= 1) {
                                return '*';
                            }

                            return mb_substr($bagian, 0, 1) . str_repeat('*', min($panjang - 1, 6));
                        })
                        ->join(' ');
                @endphp
                <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-2xl border border-gray-100 fade-up">
                    {{-- Status Banner --}}
                    <div class="px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4 
                        @if($hasil->status_ppdb === 'diterima') bg-green-500 @elseif($hasil->status_ppdb === 'ditolak') bg-red-500 @else bg-yellow-500 @endif text-white">
                        <div class="flex items-center gap-4 text-center md:text-left">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-2xl">
                                @if($hasil->status_ppdb === 'diterima') <i class="fas fa-check-circle"></i> @elseif($hasil->status_ppdb === 'ditolak') <i class="fas fa-times-circle"></i> @else <i class="fas fa-clock"></i> @endif
                            </div>
                            <div>
                                <p class="text-white/80 text-xs font-bold uppercase tracking-widest">Status Pendaftaran</p>
                                <h4 class="text-xl font-black uppercase tracking-tight">
                                    @if($hasil->status_ppdb === 'diterima') Lolos Seleksi @elseif($hasil->status_ppdb === 'ditolak') Tidak Lolos @else Sedang Diverifikasi @endif
                                </h4>
                            </div>
                        </div>
                        <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/20 text-center">
                            <p class="text-white/70 text-[10px] font-bold uppercase">Data Pendaftaran</p>
                            <p class="font-black">Ditemukan</p>
                        </div>
                    </div>

                    {{-- Data Grid --}}
                    <div class="p-8 md:p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-2">
                                        <i class="fas fa-user-shield text-[var(--color-accent)]"></i> Ringkasan Pendaftar
                                    </span>
                                    <h5 class="text-lg font-bold text-[var(--color-primary)]">{{ $namaPublik ?: 'Data ditemukan' }}</h5>
                                    <p class="text-gray-400 text-xs">Detail pribadi hanya dapat dikonfirmasi melalui panitia PPDB.</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Tanggal Daftar</span>
                                        <span class="text-sm font-bold text-[var(--color-primary)]">{{ $hasil->tanggal_daftar?->format('d F Y') ?? '-' }}</span>
                                    </div>
                                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase block mb-1">Status Berkas</span>
                                        <span class="text-sm font-bold text-[var(--color-primary)]">
                                            @if($hasil->status_ppdb === 'diterima') Disetujui @elseif($hasil->status_ppdb === 'ditolak') Ditolak @else Menunggu @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-2">
                                        <i class="fas fa-info-circle text-[var(--color-accent)]"></i> Informasi Tambahan
                                    </span>
                                    <div class="space-y-3 mt-4">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                                            <span class="text-xs text-gray-500">Nomor Induk</span>
                                            <span class="text-xs font-bold text-[var(--color-primary)]">Terverifikasi oleh sistem</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-xs text-gray-500">Terakhir Update</span>
                                            <span class="text-xs font-bold text-[var(--color-primary)]">{{ $hasil->tgl_verifikasi?->format('d M Y') ?? 'Belum Diverifikasi' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Note --}}
                        <div class="mt-10 pt-8 border-t border-gray-100">
                            @if($hasil->status_ppdb === 'diterima')
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl">
                                    <p class="text-green-800 text-sm leading-relaxed">
                                        <strong>Selamat!</strong> Pendaftaran Anda telah disetujui. Silahkan hubungi panitia PPDB atau datang langsung ke madrasah untuk proses daftar ulang.
                                    </p>
                                </div>
                            @elseif($hasil->status_ppdb === 'ditolak')
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                                    <p class="text-red-800 text-sm leading-relaxed">
                                        <strong>Mohon Maaf.</strong> Pendaftaran Anda belum dapat kami proses lebih lanjut. Silahkan hubungi panitia untuk informasi lebih detail.
                                    </p>
                                </div>
                            @else
                                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-xl">
                                    <p class="text-yellow-800 text-sm leading-relaxed">
                                        <strong>Informasi:</strong> Data pendaftaran Anda sudah masuk ke sistem kami. Panitia sedang melakukan verifikasi berkas. Mohon cek kembali halaman ini secara berkala.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white rounded-[2.5rem] p-12 text-center shadow-xl border border-gray-100 fade-up">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search-minus text-3xl text-gray-300"></i>
                    </div>
                    <h4 class="text-xl font-bold text-[var(--color-primary)] mb-2">Data Tidak Ditemukan</h4>
                    <p class="text-gray-400 max-w-sm mx-auto leading-relaxed">
                        Maaf, kami tidak menemukan data pendaftaran untuk kata kunci <span class="text-[var(--color-accent)] font-bold">"{{ $cari }}"</span>. Pastikan nomor yang dimasukkan sudah benar.
                    </p>
                    <a href="{{ route('cek-pendaftaran') }}" class="inline-block mt-6 text-sm font-bold text-[var(--color-primary)] hover:text-[var(--color-accent)] transition">Coba cari kembali &rarr;</a>
                </div>
                @endif
            </div>
        @endif

        {{-- Help Section --}}
        <div class="mt-20 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="fas fa-question-circle text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <div class="shrink-0">
                    <div class="w-16 h-16 bg-[var(--color-accent)] rounded-2xl flex items-center justify-center text-2xl shadow-xl shadow-black/20">
                        <i class="fas fa-headset text-[var(--color-primary)]"></i>
                    </div>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-xl font-bold mb-2">Butuh Bantuan Panitia?</h4>
                    <p class="text-green-100 text-sm leading-relaxed">Jika Anda mengalami kendala saat melakukan pengecekan atau menemukan kesalahan data, jangan ragu untuk menghubungi layanan informasi kami.</p>
                </div>
                <a href="https://www.instagram.com/mi_annajiyah?igsh=MTF2ZzloN2tjenoybg==" target="_blank" class="bg-white text-[var(--color-primary)] px-6 py-3 rounded-xl font-bold hover:bg-[var(--color-accent)] transition whitespace-nowrap shadow-xl">
                    Hubungi Panitia
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
