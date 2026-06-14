<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - MI Annajiyah</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="font-sans min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #0b3b1e 0%, #1a6b30 50%, #0a2a14 100%);">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-[var(--color-accent)]/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-4">
        <div class="text-center mb-8 fade-up">
            <img src="{{ asset('logo-web.webp') }}" alt="Logo" class="w-20 h-20 rounded-full mx-auto border-4 border-[var(--color-accent)] shadow-xl mb-4" width="80" height="80" decoding="async">
            <h1 class="text-3xl font-black text-white">Admin Panel</h1>
            <p class="text-green-200 text-sm mt-1">MI Annajiyah</p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/20 shadow-2xl fade-up delay-2">
            <h2 class="text-xl font-bold text-white mb-6 text-center">
                <i class="fas fa-lock mr-2 text-[var(--color-accent)]"></i>Masuk ke Sistem
            </h2>

            @if($errors->has('login'))
            <div class="bg-red-500/20 border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-4 text-sm animate-slide">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first('login') }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-5">
                    <label class="text-white text-sm font-semibold mb-2 block"><i class="fas fa-user mr-1"></i> Username</label>
                    <input type="text" name="username" required autofocus value="{{ old('username') }}"
                           class="w-full px-4 py-3 rounded-xl bg-white/20 text-white placeholder-white/40 border border-white/30 focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/30 outline-none transition"
                           placeholder="Masukkan username">
                </div>
                <div class="mb-6">
                    <label class="text-white text-sm font-semibold mb-2 block"><i class="fas fa-lock mr-1"></i> Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl bg-white/20 text-white placeholder-white/40 border border-white/30 focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/30 outline-none transition"
                           placeholder="Masukkan password">
                </div>
                <button type="submit" class="w-full bg-[var(--color-accent)] text-[var(--color-primary)] py-3 rounded-xl font-bold text-lg hover:bg-[var(--color-accent-dark)] transition transform hover:scale-[1.02] shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </form>
        </div>
        <p class="text-center text-green-300/50 text-xs mt-6">&copy; {{ date('Y') }} MI Annajiyah</p>
    </div>
</body>
</html>
