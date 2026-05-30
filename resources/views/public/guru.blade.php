@extends('layouts.public')
@section('title', 'Tenaga Pendidik - MI Annajiyah')
@section('meta_description', 'Profil lengkap tenaga pendidik dan staf MI Annajiyah - Guru-guru profesional berdedikasi tinggi.')

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] pt-32 pb-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-[var(--color-accent)]/20 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-4 border border-[var(--color-accent)]/30">PROFIL SDM</span>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-3">Tenaga Pendidik Kami</h1>
        <p class="text-green-200 text-lg max-w-2xl mx-auto">Guru-guru profesional berdedikasi tinggi — klik profil untuk melihat detail</p>
    </div>
</div>

{{-- Guru Grid with Lightbox --}}
<section class="py-16 bg-gray-50" x-data="guruLightbox()">
    <div class="max-w-6xl mx-auto px-4">
        @if($guruList->count() > 0)
        @php
            $kepsek = $guruList->firstWhere('jabatan', 'Kepala Sekolah');
            $teachers = $guruList->filter(fn($g) => $g->jabatan !== 'Kepala Sekolah');
            if (!$kepsek && $guruList->count() > 0) {
                $kepsek = $guruList->first();
                $teachers = $guruList->skip(1);
            }
        @endphp

        {{-- Kepala Sekolah --}}
        @if($kepsek)
        <div class="flex justify-center mb-12">
            <div class="w-full sm:w-1/2 lg:w-1/4">
                <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group cursor-pointer border-2 border-[var(--color-accent)]/30 fade-up"
                     @@click="open(
                         '{{ $kepsek->foto ? asset(\App\Helpers\ImageHelper::getWebp($kepsek->foto)) : '' }}',
                         '{{ addslashes($kepsek->nama) }}',
                         'Kepala Sekolah',
                         '{{ addslashes($kepsek->mapel ?? '') }}',
                         '{{ addslashes($kepsek->nip ?? '') }}'
                     )">
                    <div class="p-4">
                        <div class="relative bg-gradient-to-br from-green-50 to-green-100 aspect-square rounded-2xl overflow-hidden shadow-inner">
                            @if($kepsek->foto)
                            <img src="{{ asset(\App\Helpers\ImageHelper::getCard($kepsek->foto)) }}"
                                 alt="{{ $kepsek->nama }}"
                                 class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-110"
                                 width="480"
                                 height="480"
                                 loading="eager"
                                 decoding="async"
                                 fetchpriority="high"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                                <i class="fas fa-user text-green-400" style="font-size:4rem;"></i>
                            </div>
                            @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                                <i class="fas fa-user text-green-400" style="font-size:4rem;"></i>
                            </div>
                            @endif
                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="w-11 h-11 bg-white/90 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-circle text-[var(--color-primary)] text-xl"></i>
                                </div>
                            </div>
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
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 reveal reveal-zoom delay-{{ ($loop->index % 4) + 1 }} group cursor-pointer"
                 @@click="open(
                     '{{ $guru->foto ? asset(\App\Helpers\ImageHelper::getWebp($guru->foto)) : '' }}',
                     '{{ addslashes($guru->nama) }}',
                     '{{ addslashes($guru->jabatan ?? $guru->mapel) }}',
                     '{{ addslashes($guru->mapel) }}',
                     '{{ $guru->nip ?: '-' }}'
                 )">
                {{-- Photo --}}
                <div class="p-4">
                    <div class="relative bg-gradient-to-br from-green-50 to-green-100 aspect-square rounded-2xl overflow-hidden shadow-inner">
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
                            <i class="fas fa-user text-green-400" style="font-size:4rem;"></i>
                        </div>
                        @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                            <i class="fas fa-user text-green-400" style="font-size:4rem;"></i>
                        </div>
                        @endif

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="w-11 h-11 bg-white/90 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-circle text-[var(--color-primary)] text-xl"></i>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Info --}}
                <div class="p-4 text-center">
                    <h3 class="font-bold text-[var(--color-primary)] text-sm leading-snug">{{ $guru->nama }}</h3>
                    <p class="text-xs text-[var(--color-accent)] font-semibold mt-1 flex items-center justify-center gap-1">
                        <i class="fas fa-chalkboard-teacher text-xs"></i>
                        {{ $guru->jabatan ?? $guru->mapel }}
                    </p>
                    @if($guru->jabatan && $guru->jabatan !== $guru->mapel)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $guru->mapel }}</p>
                    @endif
                    <p class="text-xs text-gray-300 mt-2 italic">Klik untuk detail</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-400 text-lg">Data tenaga pendidik belum tersedia.</p>
        </div>
        @endif
    </div>

    {{-- ===== GURU LIGHTBOX MODAL ===== --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         @@keydown.escape.window="close()"
         style="display:none;">

        {{-- Solid dark overlay --}}
        <div class="absolute inset-0 bg-black/95 backdrop-blur-md" @@click="close()"></div>

        {{-- Modal card --}}
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-sm w-full"
             style="z-index:1;">

            {{-- Photo --}}
            <div class="p-4">
                <div class="relative bg-gray-50 aspect-square rounded-2xl overflow-hidden shadow-inner border border-gray-100">
                    <img :src="currentImg || '{{ asset('logo.png') }}'" :alt="currentName"
                         x-show="currentImg"
                         class="w-full h-full object-cover object-top"
                         onerror="this.style.display='none'">
                    <div x-show="!currentImg"
                         class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-200" style="font-size:4rem;"></i>
                    </div>
                </div>
            </div>

            {{-- Detail Info --}}
            <div class="px-6 pb-6">
                <div class="text-center mb-4">
                    <h2 class="text-lg font-black text-[var(--color-primary)]" x-text="currentName"></h2>
                    <p class="text-[var(--color-accent)] font-bold text-xs uppercase tracking-wider mt-1" x-text="currentJabatan"></p>
                </div>

                <div class="space-y-2">
                    <div x-show="currentNip" class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Nomor Induk (NIP)</span>
                        <span class="text-xs font-bold text-[var(--color-primary)]" x-text="currentNip"></span>
                    </div>
                    <div x-show="currentMapel" class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Mata Pelajaran</span>
                        <span class="text-xs font-bold text-[var(--color-primary)]" x-text="currentMapel"></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Unit Kerja</span>
                        <span class="text-xs font-bold text-[var(--color-primary)]">MI Annajiyah</span>
                    </div>
                </div>

                <button @@click="close()" class="w-full mt-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition-all">
                    Tutup Profil
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Ketua Yayasan Section --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-10">
            <span class="inline-block bg-green-100 text-[var(--color-primary)] px-4 py-1 rounded-full text-sm font-semibold mb-3">PIMPINAN</span>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-primary)]">Ketua Yayasan</h2>
            <div class="w-16 h-1 bg-[var(--color-accent)] mx-auto mt-3 rounded-full"></div>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-8 bg-gradient-to-br from-green-50 to-white rounded-3xl p-8 shadow-lg border border-green-100">
            <div class="shrink-0">
                <div class="w-44 h-44 rounded-full overflow-hidden border-4 border-[var(--color-accent)] shadow-xl">
                    @if(file_exists(public_path('uploads/ketua_yayasan.webp')))
                        <img src="{{ asset('uploads/ketua_yayasan.webp') }}"
                             alt="Ketua Yayasan"
                             class="w-full h-full object-cover object-center"
                             width="176"
                             height="176"
                             loading="lazy"
                             decoding="async">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                            <i class="fas fa-user text-green-400 text-5xl"></i>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-black text-[var(--color-primary)] mb-1">H. Idris Rosyadi, S.Pd.I</h3>
                <p class="text-[var(--color-accent)] font-semibold mb-4 flex items-center gap-2">
                    <i class="fas fa-star"></i> Ketua Yayasan Pendidikan Islam Annajiyah
                </p>
                <p class="text-gray-600 leading-relaxed">Yayasan Pendidikan Islam Annajiyah berkomitmen untuk terus meningkatkan kualitas pendidikan berbasis Islam yang menghasilkan generasi yang cerdas, berakhlak mulia, dan siap menghadapi tantangan zaman.</p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function guruLightbox() {
    return {
        isOpen: false,
        currentImg: '',
        currentName: '',
        currentJabatan: '',
        currentMapel: '',
        currentNip: '',
        open(img, name, jabatan, mapel, nip) {
            this.currentImg     = img;
            this.currentName    = name;
            this.currentJabatan = jabatan;
            this.currentMapel   = mapel;
            this.currentNip     = nip;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.isOpen = false;
            document.body.style.overflow = '';
        }
    }
}
</script>
@endpush
@endsection
