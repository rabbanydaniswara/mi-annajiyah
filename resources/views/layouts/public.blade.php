<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'MI Annajiyah - Madrasah Ibtidaiyah Unggulan, Pendaftaran Peserta Didik Baru.')">
    <title>@yield('title', 'MI Annajiyah - Madrasah Unggulan')</title>

    {{-- Open Graph / Twitter Card --}}
    <meta property="og:title" content="@yield('title', 'MI Annajiyah - Madrasah Unggulan')">
    <meta property="og:description" content="@yield('meta_description', 'MI Annajiyah - Madrasah Ibtidaiyah Unggulan, Pendaftaran Peserta Didik Baru.')">
    <meta property="og:image" content="@yield('og_image', asset('logo.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'MI Annajiyah - Madrasah Unggulan')">
    <meta name="twitter:description" content="@yield('meta_description', 'MI Annajiyah - Madrasah Ibtidaiyah Unggulan di Pondok Aren, Tangerang Selatan. Mencetak generasi berakhlak mulia.')">
    <meta name="twitter:image" content="@yield('og_image', asset('logo.png'))">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    @vite(['resources/css/public.css', 'resources/js/public.js'])
    @stack('styles')

    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "School",
      "name": "MI Annajiyah",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('logo.png') }}",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "{{ $kontenWeb['alamat'] ?? 'Jl. PLN No. 80, Pondok Karya' }}",
        "addressLocality": "Pondok Aren",
        "addressRegion": "Tangerang Selatan",
        "postalCode": "15225",
        "addressCountry": "ID"
      },
      "contactPoint": {
        "@@type": "ContactPoint",
        "telephone": "{{ $kontenWeb['telepon'] ?? '' }}",
        "contactType": "customer service"
      }
    }
    </script>
    @yield('schema_org')
    <noscript>
        <style>
            .reveal { opacity: 1 !important; transform: none !important; }
        </style>
    </noscript>
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800 overflow-x-hidden">
    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="navbar"
         x-data="{ open: false, scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
         :class="scrolled ? 'bg-[var(--color-primary)] shadow-lg py-2' : 'bg-transparent py-4'">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo-web.webp') }}" alt="Logo MI Annajiyah" class="w-10 h-10 rounded-full border-2 border-[var(--color-accent)]" width="40" height="40">
                <div>
                    <div class="text-white font-bold text-lg leading-tight">MI Annajiyah</div>
                    <p class="text-green-200 text-xs">Madrasah Ibtidaiyah Terakreditasi B</p>
                </div>
            </a>

            {{-- Desktop Menu --}}
            <div class="hidden lg:flex items-center gap-5">
                <a href="{{ route('home') }}" class="text-white/90 hover:text-[var(--color-accent)] transition font-medium text-sm {{ request()->routeIs('home') ? 'text-[var(--color-accent)]' : '' }}">Beranda</a>
                <a href="{{ route('home') }}#tentang" class="text-white/90 hover:text-[var(--color-accent)] transition font-medium text-sm">Visi & Misi</a>
                <a href="{{ route('fasilitas') }}" class="text-white/90 hover:text-[var(--color-accent)] transition font-medium text-sm {{ request()->routeIs('fasilitas') ? 'text-[var(--color-accent)]' : '' }}">Fasilitas</a>
                <a href="{{ route('kegiatan') }}" class="text-white/90 hover:text-[var(--color-accent)] transition font-medium text-sm {{ request()->routeIs('kegiatan') ? 'text-[var(--color-accent)]' : '' }}">Kegiatan</a>
                <a href="{{ route('tenaga-pendidik') }}" class="text-white/90 hover:text-[var(--color-accent)] transition font-medium text-sm {{ request()->routeIs('tenaga-pendidik') ? 'text-[var(--color-accent)]' : '' }}">Tenaga Pendidik</a>
                <a href="{{ route('pendaftaran') }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2 rounded-full font-bold text-sm hover:bg-[var(--color-accent-dark)] transition transform hover:scale-105 shadow-md">
                    <i class="fas {{ $ppdbSettings['is_open'] ? 'fa-edit' : 'fa-lock' }} mr-1"></i>
                    PPDB {{ $ppdbSettings['is_open'] ? 'Dibuka' : 'Ditutup' }}
                </a>
                <a href="{{ route('cek-pendaftaran') }}" class="border-2 border-white/70 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-white hover:text-[var(--color-primary)] transition">
                    <i class="fas fa-search mr-1"></i> Cek Status
                </a>
            </div>

            {{-- Mobile Toggle --}}
            <button @@click="open = !open" class="lg:hidden text-white text-2xl" aria-label="Buka atau tutup menu navigasi" :aria-expanded="open.toString()">
                <i :class="open ? 'fas fa-times' : 'fas fa-bars'"></i>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="open" x-transition class="lg:hidden bg-[var(--color-primary)] border-t border-green-800 mt-2">
            <div class="flex flex-col py-4 px-6 gap-3">
                <a href="{{ route('home') }}" class="text-white/90 hover:text-[var(--color-accent)] py-2" @@click="open=false">Beranda</a>
                <a href="{{ route('home') }}#tentang" class="text-white/90 hover:text-[var(--color-accent)] py-2" @@click="open=false">Visi & Misi</a>
                <a href="{{ route('fasilitas') }}" class="text-white/90 hover:text-[var(--color-accent)] py-2" @@click="open=false">Fasilitas</a>
                <a href="{{ route('kegiatan') }}" class="text-white/90 hover:text-[var(--color-accent)] py-2" @@click="open=false">Kegiatan</a>
                <a href="{{ route('tenaga-pendidik') }}" class="text-white/90 hover:text-[var(--color-accent)] py-2" @@click="open=false">Tenaga Pendidik</a>
                <a href="{{ route('pendaftaran') }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-4 py-2 rounded-full font-bold text-center" @@click="open=false">PPDB {{ $ppdbSettings['is_open'] ? 'Dibuka' : 'Ditutup' }}</a>
                <a href="{{ route('cek-pendaftaran') }}" class="border-2 border-white/70 text-white px-4 py-2 rounded-full font-semibold text-center" @@click="open=false">Cek Status</a>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-[var(--color-primary)] text-white pt-16 pb-6">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">
            <div class="md:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('logo-web.webp') }}" alt="Logo" class="w-12 h-12 rounded-full border-2 border-[var(--color-accent)]" width="48" height="48" loading="lazy" decoding="async">
                    <div>
                        <h3 class="font-bold text-lg">MI Annajiyah</h3>
                        <p class="text-green-300 text-xs">Madrasah Ibtidaiyah</p>
                    </div>
                </div>
                <p class="text-green-200 text-sm leading-relaxed">Madrasah Ibtidaiyah yang berkomitmen mencetak generasi berakhlak mulia dan berprestasi.</p>
                <div class="flex gap-3 mt-4">
                    <a href="{{ $kontenWeb['ig'] ?? 'https://www.instagram.com/mi_annajiyah?igsh=MTF2ZzloN2tjenoybg==' }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[var(--color-accent)] hover:text-[var(--color-primary)] flex items-center justify-center transition text-sm shadow-sm" title="Instagram MI Annajiyah" aria-label="Instagram MI Annajiyah">
                        <svg class="w-4 h-4" viewBox="0 0 448 512" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
                        </svg>
                    </a>
                    <a href="{{ $kontenWeb['tiktok'] ?? 'https://www.tiktok.com/@mis.annajiyah' }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[var(--color-accent)] hover:text-[var(--color-primary)] flex items-center justify-center transition text-sm shadow-sm" title="TikTok MI Annajiyah" aria-label="TikTok MI Annajiyah">
                        <svg class="w-4 h-4" viewBox="0 0 448 512" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-[var(--color-accent)]">Menu</h3>
                <ul class="space-y-2 text-green-200 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>Beranda</a></li>
                    <li><a href="{{ route('fasilitas') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>Fasilitas</a></li>
                    <li><a href="{{ route('kegiatan') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>Kegiatan</a></li>
                    <li><a href="{{ route('tenaga-pendidik') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>Tenaga Pendidik</a></li>
                    <li><a href="{{ route('pendaftaran') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>PPDB {{ $ppdbSettings['academic_year'] }}</a></li>
                    <li><a href="{{ route('cek-pendaftaran') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>Cek Pendaftaran</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-[var(--color-accent)]">Kontak</h3>
                <ul class="space-y-3 text-green-200 text-sm">
                    <li class="flex gap-2">
                        <i class="fas fa-map-marker-alt text-[var(--color-accent)] mt-0.5"></i>
                        <span>{{ $kontenWeb['alamat'] ?? 'Jl. PLN No. 80, Pondok Karya, Pondok Aren, Tangerang Selatan' }}</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-phone text-[var(--color-accent)]"></i>
                        <span>{{ $kontenWeb['telepon'] ?? '+62 851-xxxx-xxxx' }}</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-envelope text-[var(--color-accent)]"></i>
                        <span>{{ $kontenWeb['email'] ?? 'info@miannajiyah.sch.id' }}</span>
                    </li>
                    @if($whatsappUrl = \App\Helpers\PhoneHelper::whatsappUrl($kontenWeb['wa'] ?? null))
                    <li class="flex gap-2">
                        <i class="fab fa-whatsapp text-[var(--color-accent)]"></i>
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--color-accent)] transition">{{ $kontenWeb['wa'] }}</a>
                    </li>
                    @endif
                    <li class="flex gap-2 items-center">
                        <svg class="w-4 h-4 text-[var(--color-accent)] shrink-0" viewBox="0 0 448 512" fill="currentColor" aria-hidden="true" focusable="false">
                            <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
                        </svg>
                        <a href="{{ $kontenWeb['ig'] ?? 'https://www.instagram.com/mi_annajiyah?igsh=MTF2ZzloN2tjenoybg==' }}" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--color-accent)] transition">Instagram</a>
                    </li>
                    <li class="flex gap-2 items-center">
                        <svg class="w-4 h-4 text-[var(--color-accent)] shrink-0" viewBox="0 0 448 512" fill="currentColor" aria-hidden="true" focusable="false">
                            <path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/>
                        </svg>
                        <a href="{{ $kontenWeb['tiktok'] ?? 'https://www.tiktok.com/@mis.annajiyah' }}" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--color-accent)] transition">TikTok</a>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-[var(--color-accent)]">Jam Operasional</h3>
                <p class="text-green-200 text-sm leading-relaxed whitespace-pre-line">{{ $kontenWeb['jam_op'] ?? 'Senin - Jumat: 07.00 - 13.30 WIB; Sabtu: 07.00 - 11.00 WIB' }}</p>
            </div>
        </div>
        <div class="border-t border-green-800 pt-6 text-center text-green-300 text-sm">
            <p>&copy; {{ date('Y') }} MI Annajiyah. All Rights Reserved. | Dibuat oleh TIM UNPAM</p>
        </div>
    </footer>

    {{-- Scroll to Top --}}
    <button type="button" class="scroll-top bg-[var(--color-accent)] text-[var(--color-primary)]" id="scrollTop"
            onclick="window.scrollTo({top:0, behavior:'smooth'})" aria-label="Kembali ke atas">
        <i class="fas fa-arrow-up font-bold"></i>
    </button>

    <script>
        window.addEventListener('scroll', () => {
            const scrollTop = document.getElementById('scrollTop');
            if (window.scrollY > 300) { scrollTop.classList.add('show'); }
            else { scrollTop.classList.remove('show'); }
        });
    </script>
    @stack('scripts')
</body>
</html>
