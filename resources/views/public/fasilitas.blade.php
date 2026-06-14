@extends('layouts.public')
@section('title', 'Fasilitas Unggulan - MI Annajiyah')
@section('meta_description', 'Fasilitas lengkap dan unggulan MI Annajiyah untuk menunjang proses belajar mengajar yang berkualitas.')

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] pt-32 pb-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-[var(--color-accent)]/20 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-4 border border-[var(--color-accent)]/30">SARANA PRASARANA</span>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-3">Fasilitas Unggulan</h1>
        <p class="text-green-200 text-lg">Berbagai fasilitas untuk menunjang proses belajar mengajar yang nyaman dan berkualitas</p>
    </div>
</div>

{{-- Facilities Grid --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        @if($fasilitas->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($fasilitas as $fas)
            <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition hover:-translate-y-2 border border-gray-100 fade-up delay-{{ ($loop->index % 3) + 1 }}">
                @if($fas->gambar)
                <div class="h-52 overflow-hidden">
                    <img src="{{ asset(\App\Helpers\ImageHelper::getCard($fas->gambar)) }}"
                         alt="{{ $fas->nama }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                         width="560"
                         height="360"
                         loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}"
                         decoding="async"
                         fetchpriority="{{ $loop->first ? 'high' : 'auto' }}">
                </div>
                @else
                <div class="h-52 bg-gradient-to-br from-[var(--color-accent)]/10 to-[var(--color-primary)]/10 flex items-center justify-center">
                    <i class="{{ $fas->ikon ?? 'fas fa-school' }} text-6xl text-[var(--color-accent)]"></i>
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-[var(--color-accent)]/10 rounded-xl flex items-center justify-center shrink-0">
                            <i class="{{ $fas->ikon ?? 'fas fa-school' }} text-[var(--color-accent)] text-lg"></i>
                        </div>
                        <h3 class="font-bold text-[var(--color-primary)] text-lg">{{ $fas->nama }}</h3>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $fas->deskripsi }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20">
            <i class="fas fa-school text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-400 text-lg">Informasi fasilitas belum tersedia.</p>
        </div>
        @endif
    </div>
</section>

{{-- CTA --}}
<section class="py-14 bg-[var(--color-primary)]">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-black text-white mb-3">Ingin Bergabung Bersama Kami?</h2>
        <p class="text-green-200 mb-6">Daftarkan putra-putri Anda dan rasakan langsung fasilitas lengkap MI Annajiyah.</p>
        <a href="{{ route('pendaftaran') }}" class="inline-flex items-center gap-2 bg-[var(--color-accent)] text-[var(--color-primary)] px-8 py-3.5 rounded-full font-bold hover:bg-[var(--color-accent-dark)] transition transform hover:scale-105 shadow-lg">
            <i class="fas {{ $ppdbSettings['is_open'] ? 'fa-edit' : 'fa-info-circle' }}"></i>
            {{ $ppdbSettings['is_open'] ? 'Daftar PPDB Sekarang' : 'Lihat Informasi PPDB' }}
        </a>
    </div>
</section>
@endsection
