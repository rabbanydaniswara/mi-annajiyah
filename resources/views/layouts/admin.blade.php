<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MI Annajiyah</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @stack('styles')
</head>
<body class="font-sans bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen" x-data="{ 
        sidebarOpen: false, 
        deleteModal: false, 
        deleteForm: null, 
        deleteMessage: '',
        deleteTitle: 'Konfirmasi Hapus',
        deleteConfirmText: 'Hapus',
        deleteType: 'danger',
        deleteIcon: 'fa-trash'
    }">
    {{-- Mobile Menu Toggle --}}
    <div class="fixed top-4 left-4 z-[101] md:hidden">
        <button @@click="sidebarOpen = !sidebarOpen" class="w-11 h-11 bg-[var(--color-primary)] text-white rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="admin-sidebar fixed w-[280px] h-screen overflow-y-auto z-[100] transition-transform duration-300"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               style="background: linear-gradient(180deg, #0b3b1e 0%, #0a2a14 100%); box-shadow: 4px 0 20px rgba(0,0,0,0.1);">
            <div class="p-7 text-center border-b border-white/10">
                <i class="fas fa-school text-4xl text-[var(--color-accent)] animate-float"></i>
                <h2 class="text-white font-bold text-lg mt-2">Admin Panel</h2>
                <p class="text-white/60 text-xs">MI Annajiyah</p>
            </div>
            <nav class="py-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i> Dashboard
                </a>
                <a href="{{ route('admin.ppdb') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.ppdb*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-users w-6"></i> Kelola PPDB
                </a>
                <a href="{{ route('admin.konten') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.konten*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-edit w-6"></i> Kelola Konten
                </a>
                <a href="{{ route('admin.jadwal') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.jadwal*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-calendar-alt w-6"></i> Kelola Jadwal
                </a>
                <a href="{{ route('admin.guru') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.guru*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-chalkboard-user w-6"></i> Kelola Guru
                </a>
                <a href="{{ route('admin.fasilitas') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.fasilitas*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-school w-6"></i> Kelola Fasilitas
                </a>
                <a href="{{ route('admin.siswa') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.siswa*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-user-graduate w-6"></i> Kelola Siswa
                </a>
                @if(Auth::user()?->role === 'admin')
                    <a href="{{ route('admin.admin') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.admin*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                        <i class="fas fa-user-shield w-6"></i> Kelola Admin
                    </a>
                @endif
                <a href="{{ route('admin.password.edit') }}" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 relative overflow-hidden hover:bg-[var(--color-accent)]/20 hover:text-[var(--color-accent)] hover:translate-x-1 {{ request()->routeIs('admin.password.*') ? 'bg-[var(--color-accent)]/20 text-[var(--color-accent)]' : '' }}">
                    <i class="fas fa-key w-6"></i> Ganti Password
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-6 py-3 mx-3 rounded-xl text-gray-300 transition-all duration-300 hover:bg-red-500/20 hover:text-red-400 hover:translate-x-1 w-[calc(100%-24px)]">
                        <i class="fas fa-sign-out-alt w-6"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Overlay for mobile --}}
        <div x-show="sidebarOpen" @@click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-[99] md:hidden" x-transition></div>

        {{-- Main Content --}}
        <main class="flex-1 ml-0 md:ml-[280px] p-4 md:p-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white rounded-2xl p-4 shadow-sm animate-fade">
                <h2 class="text-xl font-bold text-[var(--color-primary)]">
                    <i class="fas fa-@yield('header_icon', 'home') text-[var(--color-accent)] mr-2"></i>
                    @yield('header_title', 'Dashboard')
                </h2>
                <a href="{{ route('admin.password.edit') }}" class="flex items-center gap-3 bg-gray-100 px-4 py-2 rounded-full text-sm hover:bg-gray-200 transition">
                    <i class="fas fa-user-circle text-[var(--color-primary)]"></i>
                    <span class="font-medium text-gray-700">{{ Auth::user()->username }}</span>
                </a>
            </div>

            @yield('content')
        </main>
    </div>

    {{-- Floating Toast Container --}}
    <div class="fixed top-6 right-6 z-[10000] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
        @if(session('success'))
            <x-toast type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-toast type="error" :message="session('error')" />
        @endif
        @if(session('warning'))
            <x-toast type="warning" :message="session('warning')" />
        @endif
        @if(session('info'))
            <x-toast type="info" :message="session('info')" />
        @endif
        
        {{-- For JS-triggered toasts --}}
        <template x-for="toast in $store.toasts" :key="toast.id">
            <div
                x-data="{ show: true, progress: 100 }"
                x-init="
                    setTimeout(() => {
                        let interval = setInterval(() => {
                            progress -= 2;
                            if (progress <= 0) {
                                clearInterval(interval);
                                show = false;
                            }
                        }, 50);
                    }, 100);
                "
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-10"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white border-l-4 p-4 rounded-2xl mb-3 flex items-center gap-4 shadow-xl pointer-events-auto relative overflow-hidden group min-w-[300px]"
                :class="{
                    'border-green-500': toast.type === 'success',
                    'border-red-500': toast.type === 'error',
                    'border-yellow-500': toast.type === 'warning',
                    'border-blue-500': toast.type === 'info'
                }"
                role="alert"
            >
                <div class="absolute bottom-0 left-0 h-1 transition-all duration-75" 
                     :class="{
                        'bg-green-500': toast.type === 'success',
                        'bg-red-500': toast.type === 'error',
                        'bg-yellow-500': toast.type === 'warning',
                        'bg-blue-500': toast.type === 'info'
                     }"
                     :style="'width: ' + progress + '%'"></div>
                
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     :class="{
                        'bg-green-50': toast.type === 'success',
                        'bg-red-50': toast.type === 'error',
                        'bg-yellow-50': toast.type === 'warning',
                        'bg-blue-50': toast.type === 'info'
                     }">
                    <i class="fas text-xl" :class="{
                        'fa-check-circle text-green-500': toast.type === 'success',
                        'fa-exclamation-circle text-red-500': toast.type === 'error',
                        'fa-exclamation-triangle text-yellow-500': toast.type === 'warning',
                        'fa-info-circle text-blue-500': toast.type === 'info'
                    }"></i>
                </div>
                
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-0.5" x-text="toast.type"></p>
                    <span class="font-bold text-sm leading-tight" x-text="toast.message"></span>
                </div>

                <button @@click="show = false" class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </template>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         @@keydown.escape.window.stop="if(deleteModal) { deleteModal = false; deleteForm = null; }"
         style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @@click.stop="deleteModal = false; deleteForm = null;"></div>
        <div x-show="deleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
             style="z-index: 1;">
            <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
                 :class="{
                    'bg-red-50': deleteType === 'danger',
                    'bg-green-50': deleteType === 'success',
                    'bg-yellow-50': deleteType === 'warning',
                    'bg-blue-50': deleteType === 'info'
                 }">
                <i class="fas text-2xl"
                   :class="{
                      'fa-exclamation-triangle text-red-500': deleteType === 'danger',
                      'fa-check-circle text-green-500': deleteType === 'success',
                      'fa-exclamation-circle text-yellow-500': deleteType === 'warning',
                      'fa-info-circle text-blue-500': deleteType === 'info'
                   }"></i>
            </div>
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-2" x-text="deleteTitle"></h3>
            <p class="text-gray-500 text-sm mb-6" x-text="deleteMessage"></p>
            <div class="flex gap-3 justify-center">
                <button @@click.stop="deleteModal = false; deleteForm = null;" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                    Batal
                </button>
                <button @@click.stop="if(deleteForm) deleteForm.submit(); deleteModal = false; deleteForm = null;" 
                        class="px-5 py-2.5 text-white rounded-xl font-semibold text-sm transition shadow-lg"
                        :class="{
                            'bg-red-500 hover:bg-red-600 shadow-red-500/20': deleteType === 'danger',
                            'bg-green-600 hover:bg-green-700 shadow-green-600/20': deleteType === 'success',
                            'bg-yellow-500 hover:bg-yellow-600 shadow-yellow-500/20': deleteType === 'warning',
                            'bg-blue-500 hover:bg-blue-600 shadow-blue-500/20': deleteType === 'info'
                        }">
                    <i class="fas mr-1" :class="deleteIcon"></i>
                    <span x-text="deleteConfirmText"></span>
                </button>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
