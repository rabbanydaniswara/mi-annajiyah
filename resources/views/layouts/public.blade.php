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
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="navbar"
         x-data="{ open: false, scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
         :class="scrolled ? 'bg-[var(--color-primary)] shadow-lg py-2' : 'bg-transparent py-4'">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="Logo MI Annajiyah" class="w-10 h-10 rounded-full border-2 border-[var(--color-accent)]">
                <div>
                    <h1 class="text-white font-bold text-lg leading-tight">MI Annajiyah</h1>
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
                    <i class="fas fa-edit mr-1"></i> PPDB {{ date('Y') }}
                </a>
                <a href="{{ route('cek-pendaftaran') }}" class="border-2 border-white/70 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-white hover:text-[var(--color-primary)] transition">
                    <i class="fas fa-search mr-1"></i> Cek Status
                </a>
            </div>

            {{-- Mobile Toggle --}}
            <button @@click="open = !open" class="lg:hidden text-white text-2xl">
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
                <a href="{{ route('pendaftaran') }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-4 py-2 rounded-full font-bold text-center" @@click="open=false">PPDB {{ date('Y') }}</a>
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
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-12 h-12 rounded-full border-2 border-[var(--color-accent)]" loading="lazy">
                    <div>
                        <h3 class="font-bold text-lg">MI Annajiyah</h3>
                        <p class="text-green-300 text-xs">Madrasah Ibtidaiyah</p>
                    </div>
                </div>
                <p class="text-green-200 text-sm leading-relaxed">Madrasah Ibtidaiyah yang berkomitmen mencetak generasi berakhlak mulia dan berprestasi.</p>
                <div class="flex gap-3 mt-4">
                    <a href="https://www.instagram.com/mi_annajiyah?igsh=MTF2ZzloN2tjenoybg==" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[var(--color-accent)] hover:text-[var(--color-primary)] flex items-center justify-center transition text-sm shadow-sm" title="Instagram MI Annajiyah" aria-label="Instagram MI Annajiyah">
                        <svg class="w-4 h-4" viewBox="0 0 448 512" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M224.3 141a115 115 0 1 0-.6 230 115 115 0 1 0 .6-230zm-.6 40.4a74.6 74.6 0 1 1 .6 149.2 74.6 74.6 0 1 1-.6-149.2zm93.4-45.1a26.8 26.8 0 1 1 53.6 0 26.8 26.8 0 1 1-53.6 0zm129.7 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM399 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@@mis.annajiyah?_r=1&_t=ZS-95rKvMOFVX5" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[var(--color-accent)] hover:text-[var(--color-primary)] flex items-center justify-center transition text-sm shadow-sm" title="TikTok MI Annajiyah" aria-label="TikTok MI Annajiyah">
                        <svg class="w-4 h-4" viewBox="0 0 448 512" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M448.5 209.9c-44 .1-87-13.6-122.8-39.2l0 178.7c0 33.1-10.1 65.4-29 92.6s-45.6 48-76.6 59.6-64.8 13.5-96.9 5.3-60.9-25.9-82.7-50.8-35.3-56-39-88.9 2.9-66.1 18.6-95.2 40-52.7 69.6-67.7 62.9-20.5 95.7-16l0 89.9c-15-4.7-31.1-4.6-46 .4s-27.9 14.6-37 27.3-14 28.1-13.9 43.9 5.2 31 14.5 43.7 22.4 22.1 37.4 26.9 31.1 4.8 46-.1 28-14.4 37.2-27.1 14.2-28.1 14.2-43.8l0-349.4 88 0c-.1 7.4 .6 14.9 1.9 22.2 3.1 16.3 9.4 31.9 18.7 45.7s21.3 25.6 35.2 34.6c19.9 13.1 43.2 20.1 67 20.1l0 87.4z"/>
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
                    <li><a href="{{ route('pendaftaran') }}" class="hover:text-[var(--color-accent)] transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i>PPDB {{ date('Y') }}</a></li>
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
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-[var(--color-accent)]">Jam Operasional</h3>
                <ul class="space-y-2 text-green-200 text-sm">
                    <li class="flex justify-between"><span>Senin – Jumat</span><span class="text-white font-medium">07.00 – 13.30</span></li>
                    <li class="flex justify-between"><span>Sabtu</span><span class="text-white font-medium">07.00 – 11.00</span></li>
                    <li class="flex justify-between"><span>Minggu</span><span class="text-red-300 font-medium">Libur</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-green-800 pt-6 text-center text-green-300 text-sm">
            <p>&copy; {{ date('Y') }} MI Annajiyah. All Rights Reserved. | Dibuat oleh TIM UNPAM</p>
        </div>
    </footer>

    {{-- Scroll to Top --}}
    <div class="scroll-top bg-[var(--color-accent)] text-[var(--color-primary)]" id="scrollTop"
         onclick="window.scrollTo({top:0, behavior:'smooth'})">
        <i class="fas fa-arrow-up font-bold"></i>
    </div>

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
