@extends('layouts.public')
@section('title', 'MI Annajiyah - Madrasah Ibtidaiyah Unggulan')
@section('meta_description', 'MI Annajiyah - Madrasah Ibtidaiyah Unggulan di Pondok Aren, Tangerang Selatan. Mencetak generasi berakhlak mulia dan berprestasi.')

@push('head')
    @if($banners->count() > 0)
        <link rel="preload" as="image" href="{{ asset(\App\Helpers\ImageHelper::getHero($banners->first()->gambar ?? 'depan.jpg')) }}">
    @endif
@endpush

@section('content')
<div x-data="indexData()" x-init="startSlider()">
{{-- ==================== HERO SECTION ==================== --}}
<section id="beranda" class="relative h-screen overflow-hidden">
    @foreach($banners as $index => $banner)
    @php
        $heroImage = \App\Helpers\ImageHelper::getHero($banner->gambar ?? 'depan.jpg');
    @endphp
    <div class="slide absolute inset-0 {{ $index === 0 ? 'active' : '' }}">
        <img
            src="{{ $index === 0 ? asset($heroImage) : \App\Helpers\ImageHelper::transparentPixel() }}"
            data-hero-src="{{ $index === 0 ? '' : asset($heroImage) }}"
            alt="{{ $banner->judul ?? 'MI Annajiyah' }}"
            class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700"
            width="1600"
            height="900"
            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
            decoding="{{ $index === 0 ? 'sync' : 'async' }}"
            fetchpriority="{{ $index === 0 ? 'high' : 'low' }}">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-[var(--color-primary)]/80"></div>
        <div class="relative z-10 flex items-center justify-center h-full">
            <div class="text-center text-white px-4 max-w-4xl">
                <div class="inline-block bg-[var(--color-accent)]/20 border border-[var(--color-accent)]/50 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-5 backdrop-blur-sm fade-up">
                    PPDB {{ date('Y') }}/{{ date('Y')+1 }} Telah Dibuka
                </div>
                <h2 class="text-4xl md:text-7xl font-black leading-tight mb-4 animate-slide-up drop-shadow-[0_4px_4px_rgba(0,0,0,0.6)]">{{ $banner->judul }}</h2>
                <p class="text-lg md:text-2xl text-green-50 mb-8 animate-slide-up delay-100 drop-shadow-[0_2px_2px_rgba(0,0,0,0.4)]">{{ $banner->subtitle }}</p>
                <div class="flex flex-wrap justify-center gap-4 fade-up delay-3">
                    <a href="{{ route('pendaftaran') }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-8 py-3.5 rounded-full font-bold hover:bg-[var(--color-accent-dark)] transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-edit mr-2"></i> Daftar Sekarang
                    </a>
                    <a href="#tentang" class="border-2 border-white text-white px-8 py-3.5 rounded-full font-bold hover:bg-white hover:text-[var(--color-primary)] transition">
                        <i class="fas fa-info-circle mr-2"></i> Tentang Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {{-- Slider indicators --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-20">
        @foreach($banners as $index => $banner)
        <button @@click="goTo({{ $index }})" :class="current === {{ $index }} ? 'bg-[var(--color-accent)] w-8' : 'bg-white/50 w-3'" class="h-3 rounded-full transition-all duration-300"></button>
        @endforeach
    </div>
    {{-- Scroll down indicator --}}
    <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-20 animate-bounce">
        <a href="#stats"><i class="fas fa-chevron-down text-white/60 text-xl"></i></a>
    </div>
</section>

{{-- ==================== STATS SECTION ==================== --}}
<section id="stats" class="bg-[var(--color-primary)] py-12">
    <div class="max-w-6xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="text-center text-white reveal delay-1">
            <div class="text-3xl md:text-4xl font-black text-[var(--color-accent)]">{{ $totalSiswa }}+</div>
            <p class="text-green-200 text-sm mt-1">Total Siswa</p>
        </div>
        <div class="text-center text-white reveal delay-2">
            <div class="text-3xl md:text-4xl font-black text-[var(--color-accent)]">{{ $totalGuru }}</div>
            <p class="text-green-200 text-sm mt-1">Guru & Staff</p>
        </div>
        <div class="text-center text-white reveal delay-3">
            <div class="text-3xl md:text-4xl font-black text-[var(--color-accent)]">{{ $totalPendaftar }}</div>
            <p class="text-green-200 text-sm mt-1">Pendaftar Baru</p>
        </div>
        <div class="text-center text-white reveal delay-4">
            <div class="text-3xl md:text-4xl font-black text-[var(--color-accent)]">1995</div>
            <p class="text-green-200 text-sm mt-1">Tahun Berdiri</p>
        </div>
    </div>
</section>

{{-- ==================== TENTANG / VISI MISI SECTION ==================== --}}
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-14 reveal">
            <span class="inline-block bg-green-100 text-[var(--color-primary)] px-4 py-1 rounded-full text-sm font-semibold mb-3">TENTANG KAMI</span>
            <h2 class="text-3xl md:text-4xl font-black text-[var(--color-primary)]">Mengenal MI Annajiyah</h2>
            <div class="w-20 h-1 bg-[var(--color-accent)] mx-auto mt-4 rounded-full"></div>
            <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Madrasah Ibtidaiyah yang berkomitmen menghadirkan pendidikan berkualitas berbasis nilai-nilai Islam sejak tahun 1995.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition hover:-translate-y-1 reveal reveal-left delay-1 border border-green-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-[var(--color-primary)] rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-primary)]">Visi</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">{{ $visi->konten ?? 'Menjadikan Madrasah Ibtidaiyah yang berkualitas, berkarakter islami dan berprestasi.' }}</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition hover:-translate-y-1 reveal reveal-right delay-2 border border-yellow-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-[var(--color-accent)] rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-bullseye text-[var(--color-primary)] text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--color-primary)]">Misi</h3>
                </div>
                <div class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $misi->konten ?? '' }}</div>
            </div>
        </div>
        @if($sejarah && $sejarah->konten)
        <div class="bg-gradient-to-r from-[var(--color-primary)]/5 to-[var(--color-primary)]/10 rounded-2xl p-8 border border-[var(--color-primary)]/10 fade-up delay-3">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-[var(--color-primary)] rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-history text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-[var(--color-primary)]">Sejarah Singkat</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">{{ $sejarah->konten }}</p>
        </div>
        @endif
    </div>
</section>

{{-- ==================== FASILITAS SECTION ==================== --}}
<section id="fasilitas" class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-14 reveal">
            <span class="inline-block bg-green-100 text-[var(--color-primary)] px-4 py-1 rounded-full text-sm font-semibold mb-3">FASILITAS</span>
            <h2 class="text-3xl md:text-4xl font-black text-[var(--color-primary)]">Fasilitas Unggulan</h2>
            <div class="w-20 h-1 bg-[var(--color-accent)] mx-auto mt-4 rounded-full"></div>
            <p class="text-gray-500 mt-4">Berbagai fasilitas untuk menunjang proses belajar mengajar</p>
        </div>
        @if($fasilitas->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($fasilitas as $fas)
            <div class="bg-white rounded-2xl p-7 text-center shadow-md hover:shadow-xl transition hover:-translate-y-2 border border-gray-100 reveal reveal-zoom delay-{{ ($loop->index % 3) + 1 }}">
                <div class="w-16 h-16 bg-[var(--color-accent)]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="{{ $fas->ikon ?? 'fas fa-school' }} text-3xl text-[var(--color-accent)]"></i>
                </div>
                <h3 class="font-bold text-[var(--color-primary)] text-lg mb-2">{{ $fas->nama }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $fas->deskripsi }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ==================== KEGIATAN SECTION ==================== --}}
<section id="kegiatan" class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-14 reveal">
            <span class="inline-block bg-green-100 text-[var(--color-primary)] px-4 py-1 rounded-full text-sm font-semibold mb-3">GALERI KEGIATAN</span>
            <h2 class="text-3xl md:text-4xl font-black text-[var(--color-primary)]">Kegiatan Sekolah</h2>
            <div class="w-20 h-1 bg-[var(--color-accent)] mx-auto mt-4 rounded-full"></div>
            <p class="text-gray-500 mt-4">Momen-momen berharga di MI Annajiyah</p>
        </div>
        @if($kegiatan->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($kegiatan as $kgt)
            <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition hover:-translate-y-2 border border-gray-100 reveal delay-{{ ($loop->index % 3) + 1 }} cursor-pointer"
                 @@click="openLightbox(
                     '{{ $kgt->gambar ? asset(\App\Helpers\ImageHelper::getWebp($kgt->gambar)) : '' }}',
                     '{{ addslashes($kgt->judul) }}',
                     '{{ $kgt->tanggal ? $kgt->tanggal->format('d F Y') : '-' }}',
                     '{{ addslashes($kgt->kategori->nama ?? '') }}',
                     '{{ addslashes($kgt->deskripsi ?? '') }}'
                 )">
                <div class="relative overflow-hidden h-52 bg-gradient-to-br from-green-100 to-green-200">
                    @if($kgt->gambar)
                    <img src="{{ asset(\App\Helpers\ImageHelper::getCard($kgt->gambar)) }}"
                         alt="{{ $kgt->judul }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                         width="420"
                         height="280"
                         loading="lazy"
                         decoding="async"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden w-full h-full items-center justify-center">
                        <i class="fas fa-calendar-alt text-5xl text-green-400"></i>
                    </div>
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-5xl text-green-400"></i>
                    </div>
                    @endif
                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    @if($kgt->kategori)
                    <span class="absolute top-3 left-3 bg-[var(--color-primary)] text-white text-xs px-3 py-1 rounded-full font-semibold">{{ $kgt->kategori->nama }}</span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="text-xs text-green-600 font-semibold mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>{{ $kgt->tanggal ? $kgt->tanggal->format('d M Y') : '-' }}
                    </div>
                    <h3 class="font-bold text-[var(--color-primary)] text-base mb-1">{{ $kgt->judul }}</h3>
                    @if($kgt->deskripsi)
                    <p class="text-gray-500 text-sm leading-relaxed">{{ Str::limit($kgt->deskripsi, 80) }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('kegiatan') }}" class="inline-flex items-center gap-2 bg-[var(--color-primary)] text-white px-6 py-3 rounded-full font-semibold hover:bg-[var(--color-primary-light)] transition hover:shadow-lg">
                Lihat Semua Kegiatan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @else
        <p class="text-center text-gray-400">Belum ada kegiatan yang tersedia.</p>
        @endif
    </div>
</section>

{{-- ==================== GURU SECTION ==================== --}}
<section id="guru" class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-14 reveal">
            <span class="inline-block bg-green-100 text-[var(--color-primary)] px-4 py-1 rounded-full text-sm font-semibold mb-3">TENAGA PENDIDIK</span>
            <h2 class="text-3xl md:text-4xl font-black text-[var(--color-primary)]">Tenaga Pendidik Kami</h2>
            <div class="w-20 h-1 bg-[var(--color-accent)] mx-auto mt-4 rounded-full"></div>
            <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Guru-guru profesional yang berdedikasi tinggi dalam mendidik generasi penerus bangsa</p>
        </div>
        @if($guruList->count() > 0)
        @php
            $kepsek = $guruList->firstWhere('jabatan', 'Kepala Sekolah');
            $teachers = $guruList->filter(fn($g) => $g->jabatan !== 'Kepala Sekolah')->take(6);
            if (!$kepsek && $guruList->count() > 0) {
                $kepsek = $guruList->first();
                $teachers = $guruList->skip(1)->take(6);
            }
        @endphp

        {{-- Kepala Sekolah --}}
        @if($kepsek)
        <div class="flex justify-center mb-10">
            <div class="w-full sm:w-1/2 lg:w-1/4">
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border-2 border-[var(--color-accent)]/30 reveal reveal-zoom group">
                    <div class="p-4">
                        <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-green-50 to-green-100 relative shadow-inner">
                            @if($kepsek->foto)
                            <img src="{{ asset(\App\Helpers\ImageHelper::getCard($kepsek->foto)) }}"
                                 alt="{{ $kepsek->nama }}"
                                 class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-110"
                                 width="480"
                                 height="480"
                                 loading="lazy"
                                 decoding="async"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                                <i class="fas fa-user text-green-400 text-4xl"></i>
                            </div>
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-user text-green-400 text-4xl"></i>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 text-center bg-gradient-to-b from-white to-green-50">
                        <h3 class="font-bold text-[var(--color-primary)] text-sm leading-snug">{{ $kepsek->nama }}</h3>
                        <p class="text-xs text-[var(--color-accent)] font-black mt-1 flex items-center justify-center gap-1 uppercase tracking-wider">
                             Kepala Sekolah
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Teachers Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($teachers as $guru)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 reveal reveal-zoom delay-{{ ($loop->index % 4) + 1 }} group">
                <div class="p-4">
                    <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-green-50 to-green-100 relative shadow-inner">
                        @if($guru->foto)
                        <img src="{{ asset(\App\Helpers\ImageHelper::getCard($guru->foto)) }}"
                             alt="{{ $guru->nama }}"
                             class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-110"
                             width="480"
                             height="480"
                             loading="lazy"
                             decoding="async"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-full items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                            <i class="fas fa-user text-green-400 text-4xl"></i>
                        </div>
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-user text-green-400 text-4xl"></i>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="font-bold text-[var(--color-primary)] text-sm leading-snug">{{ $guru->nama }}</h3>
                    <p class="text-xs text-[var(--color-accent)] font-semibold mt-1 flex items-center justify-center gap-1">
                        <i class="fas fa-chalkboard-teacher text-xs"></i> {{ $guru->jabatan ?? $guru->mapel }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('tenaga-pendidik') }}" class="inline-flex items-center gap-2 bg-[var(--color-primary)] text-white px-6 py-3 rounded-full font-semibold hover:bg-[var(--color-primary-light)] transition hover:shadow-lg">
                Lihat Semua Tenaga Pendidik <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endif
    </div>
</section>

{{-- ==================== MAPS SECTION ==================== --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-10">
            <span class="inline-block bg-green-100 text-[var(--color-primary)] px-4 py-1 rounded-full text-sm font-semibold mb-3">LOKASI KAMI</span>
            <h2 class="text-3xl md:text-4xl font-black text-[var(--color-primary)]">Kunjungi MI Annajiyah</h2>
            <div class="w-20 h-1 bg-[var(--color-accent)] mx-auto mt-4 rounded-full"></div>
        </div>
        <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[450px] relative group">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.9866166113837!2d106.74102917503816!3d-6.265492493723321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f06122d169cf%3A0xc6a82e9124976a26!2sJl.%20PLN%20No.80%2C%20Pondok%20Karya%2C%20Kec.%20Pondok%20Aren%2C%20Kota%20Tangerang%20Selatan%2C%20Banten%2015225!5e0!3m2!1sid!2sid!4v1714123456789!5m2!1sid!2sid" 
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-xl border border-white/50 max-w-sm transition-all duration-300 group-hover:translate-y-[-10px]">
                <h4 class="font-bold text-[var(--color-primary)] flex items-center gap-2 mb-2">
                    <i class="fas fa-map-marked-alt text-[var(--color-accent)]"></i> Alamat Sekolah
                </h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ $kontenWeb['alamat'] ?? 'JL. PLN NO.80, Pondok Karya, Pondok Aren, Tangerang Selatan' }}
                </p>
                <a href="https://maps.google.com/?q=MI+Annajiyah+Pondok+Aren" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-2 text-[var(--color-primary)] font-bold text-sm hover:text-[var(--color-accent)] transition">
                    Buka di Google Maps <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ==================== CTA PENDAFTARAN SECTION ==================== --}}
<section class="py-20 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-[var(--color-accent)]/20 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-5 border border-[var(--color-accent)]/30">PPDB {{ date('Y') }}/{{ date('Y')+1 }}</span>
        <h2 class="text-3xl md:text-5xl font-black text-white mb-4">Daftarkan Putra-Putri Anda</h2>
        <p class="text-green-200 text-lg mb-8 max-w-2xl mx-auto">Bergabunglah bersama keluarga besar MI Annajiyah dan wujudkan cita-cita generasi Islam yang cerdas dan berakhlak mulia.</p>
        <div class="flex flex-wrap justify-center gap-4 mt-10 relative z-10">
            <a href="{{ route('pendaftaran') }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-8 py-4 rounded-full font-bold text-lg hover:bg-[var(--color-accent-dark)] transition shadow-lg hover:shadow-xl">
                <i class="fas fa-edit mr-2"></i> Daftar Sekarang
            </a>
            <a href="{{ route('cek-pendaftaran') }}" class="border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-[var(--color-primary)] transition">
                <i class="fas fa-search mr-2"></i> Cek Status
            </a>
        </div>
    </div>
</section>

{{-- MODALS --}}
{{-- Lightbox Kegiatan --}}
<div x-show="isLightboxOpen" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none;">
    <div class="absolute inset-0 bg-black/95 backdrop-blur-md" @@click="closeAll()"></div>
    <div x-show="isLightboxOpen" class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col z-10" x-transition>
        <div class="relative bg-black flex-shrink-0">
            <img :src="lbImg || '{{ asset('logo.png') }}'" class="w-full max-h-[55vh] object-contain">
            <button @@click="closeAll()" class="absolute top-3 right-3 w-10 h-10 bg-black/70 text-white rounded-full flex items-center justify-center text-2xl font-bold">&times;</button>
        </div>
        <div class="p-6 overflow-y-auto">
            <h2 class="text-xl font-black text-[var(--color-primary)] mb-2" x-text="lbTitle"></h2>
            <p class="text-sm text-[var(--color-accent)] font-semibold mb-3" x-text="lbDate"></p>
            <p class="text-gray-600 text-sm leading-relaxed" x-text="lbDesc"></p>
        </div>
    </div>
</div>

{{-- Lightbox Guru --}}
<div x-show="isGuruOpen" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none;">
    <div class="absolute inset-0 bg-black/95 backdrop-blur-md" @@click="closeAll()"></div>
    <div x-show="isGuruOpen" class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-sm w-full z-10" x-transition>
        <div class="p-4">
            <div class="relative bg-gray-50 aspect-square rounded-2xl overflow-hidden border border-gray-100">
                <img :src="gImg || '{{ asset('logo.png') }}'" class="w-full h-full object-cover object-top">
            </div>
        </div>
        <div class="px-6 pb-6">
            <div class="text-center mb-4">
                <h2 class="text-lg font-black text-[var(--color-primary)]" x-text="gName"></h2>
                <p class="text-[var(--color-accent)] font-bold text-xs uppercase tracking-wider mt-1" x-text="gJabatan"></p>
            </div>
            <div class="space-y-2">
                <div x-show="gNip" class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">NIP</span>
                    <span class="text-xs font-bold text-[var(--color-primary)]" x-text="gNip"></span>
                </div>
                <div x-show="gMapel" class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Mata Pelajaran</span>
                    <span class="text-xs font-bold text-[var(--color-primary)]" x-text="gMapel"></span>
                </div>
            </div>
            <button @@click="closeAll()" class="w-full mt-6 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold">Tutup</button>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
function indexData() {
    return {
        // Hero Slider
        current: 0,
        total: {{ $banners->count() }},
        interval: null,
        startSlider() {
            if (this.total > 1) {
                this.interval = setInterval(() => this.next(), 5000);
            }
        },
        next() { this.goTo((this.current + 1) % this.total); },
        loadHeroImage(index) {
            const slide = document.querySelectorAll('.slide')[index];
            const image = slide ? slide.querySelector('img[data-hero-src]:not([data-hero-src=""])') : null;
            if (image) {
                image.src = image.dataset.heroSrc;
                image.removeAttribute('data-hero-src');
            }
        },
        goTo(index) {
            const slides = document.querySelectorAll('.slide');
            this.loadHeroImage(index);
            slides.forEach((s, i) => s.classList.toggle('active', i === index));
            this.current = index;
        },

        // Lightbox Kegiatan
        isLightboxOpen: false,
        lbImg: '', lbTitle: '', lbDate: '', lbKat: '', lbDesc: '',
        openLightbox(img, title, date, kat, desc) {
            this.lbImg = img; this.lbTitle = title; this.lbDate = date; this.lbKat = kat; this.lbDesc = desc;
            this.isLightboxOpen = true;
            document.body.style.overflow = 'hidden';
        },

        // Lightbox Guru
        isGuruOpen: false,
        gImg: '', gName: '', gJabatan: '', gMapel: '', gNip: '',
        openGuru(img, name, jabatan, mapel, nip) {
            this.gImg = img; this.gName = name; this.gJabatan = jabatan; this.gMapel = mapel; this.gNip = nip;
            this.isGuruOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeAll() {
            this.isLightboxOpen = false;
            this.isGuruOpen = false;
            document.body.style.overflow = '';
        }
    };
}
</script>
@endpush
