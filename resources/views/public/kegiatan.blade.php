@extends('layouts.public')
@section('title', 'Kegiatan Sekolah - MI Annajiyah')
@section('meta_description', 'Dokumentasi kegiatan dan program unggulan MI Annajiyah - Pramuka, Ekskul, PPDB, dan kegiatan keagamaan.')

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] pt-32 pb-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-[var(--color-accent)]/20 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-4 border border-[var(--color-accent)]/30">GALERI KEGIATAN</span>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-3">Kegiatan Sekolah</h1>
        <p class="text-green-200 text-lg max-w-2xl mx-auto">Dokumentasi lengkap kegiatan unggulan MI Annajiyah — klik foto untuk melihat detail</p>
    </div>
</div>

{{-- Filter Kategori --}}
<div class="bg-white border-b border-gray-100 sticky top-[64px] z-40 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex gap-2 overflow-x-auto">
        <a href="{{ route('kegiatan') }}"
           class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap transition
                  {{ !$filterKategori ? 'bg-[var(--color-primary)] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Semua
        </a>
        @foreach($kategoris as $kat)
        <a href="{{ route('kegiatan', ['kategori' => $kat->id]) }}"
           class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap transition
                  {{ $filterKategori == $kat->id ? 'bg-[var(--color-primary)] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $kat->nama }}
            <span class="ml-1 text-xs opacity-70">({{ $kat->kegiatan_count }})</span>
        </a>
        @endforeach
    </div>
</div>

{{-- Kegiatan Gallery Grid --}}
<section class="py-12 bg-gray-50" x-data="lightbox()">
    <div class="max-w-6xl mx-auto px-4">
        @if($kegiatan->count() > 0)
        <p class="text-sm text-gray-400 mb-6">Menampilkan <strong class="text-[var(--color-primary)]">{{ $kegiatan->total() }}</strong> kegiatan</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($kegiatan as $kgt)
            <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group cursor-pointer fade-up"
                 @@click="open(
                     '{{ $kgt->gambar ? asset(\App\Helpers\ImageHelper::getWebp($kgt->gambar)) : '' }}',
                     '{{ addslashes($kgt->judul) }}',
                     '{{ $kgt->tanggal ? $kgt->tanggal->format('d F Y') : '-' }}',
                     '{{ addslashes($kgt->kategori->nama ?? '') }}',
                     '{{ addslashes($kgt->deskripsi ?? '') }}'
                 )">
                <div class="relative overflow-hidden h-44 bg-gradient-to-br from-green-50 to-green-100">
                    @if($kgt->gambar)
                    <img src="{{ asset(\App\Helpers\ImageHelper::getCard($kgt->gambar)) }}"
                         alt="{{ $kgt->judul }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                         width="420"
                         height="280"
                         loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}"
                         decoding="async"
                         fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden w-full h-full items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                        <i class="fas fa-calendar-alt text-4xl text-green-400"></i>
                    </div>
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-100 to-green-200">
                        <i class="fas fa-calendar-alt text-4xl text-green-400"></i>
                    </div>
                    @endif
                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-expand text-[var(--color-primary)] text-lg"></i>
                        </div>
                    </div>
                    @if($kgt->kategori)
                    <span class="absolute top-2 left-2 bg-[var(--color-primary)]/90 text-white text-xs px-2 py-0.5 rounded-full font-semibold">
                        {{ $kgt->kategori->nama }}
                    </span>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="font-bold text-[var(--color-primary)] text-sm leading-snug line-clamp-2">{{ $kgt->judul }}</h3>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ $kgt->tanggal ? $kgt->tanggal->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            {{ $kegiatan->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-20">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-400 text-lg">Belum ada kegiatan yang tersedia.</p>
        </div>
        @endif
    </div>

    {{-- ===== LIGHTBOX MODAL ===== --}}
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
             class="relative bg-white rounded-3xl overflow-hidden shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col"
             style="z-index:1;">

            <div class="relative bg-black flex-shrink-0">
                <img :src="currentImg || '{{ asset('logo.png') }}'" :alt="currentTitle"
                     class="w-full max-h-[55vh] object-contain">
                <button @@click="close()"
                        class="absolute top-3 right-3 w-10 h-10 bg-black/70 hover:bg-red-500 text-white rounded-full flex items-center justify-center transition text-2xl font-bold leading-none">
                    &times;
                </button>
                <span x-show="currentKat"
                      class="absolute top-3 left-3 bg-[var(--color-primary)] text-white text-xs px-3 py-1 rounded-full font-semibold"
                      x-text="currentKat"></span>
            </div>

            <div class="p-6 overflow-y-auto">
                <h2 class="text-xl font-black text-[var(--color-primary)] mb-2" x-text="currentTitle"></h2>
                <p class="text-sm text-[var(--color-accent)] font-semibold mb-3 flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    <span x-text="currentDate"></span>
                </p>
                <p x-show="currentDesc" class="text-gray-600 text-sm leading-relaxed" x-text="currentDesc"></p>
                <p x-show="!currentDesc" class="text-gray-400 text-sm italic">Tidak ada deskripsi tambahan.</p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function lightbox() {
    return {
        isOpen: false,
        currentImg: '',
        currentTitle: '',
        currentDate: '',
        currentKat: '',
        currentDesc: '',
        open(img, title, date, kat, desc) {
            this.currentImg   = img;
            this.currentTitle = title;
            this.currentDate  = date;
            this.currentKat   = kat;
            this.currentDesc  = desc;
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
