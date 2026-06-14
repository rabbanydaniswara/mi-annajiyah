@extends('layouts.admin')
@section('title', 'Dashboard')
@section('header_icon', 'tachometer-alt')
@section('header_title', 'Dashboard')

@section('content')
{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold relative z-10"><i class="fas fa-hand-sparkles mr-2"></i>Selamat Datang, {{ Auth::user()->username }}!</h3>
    <p class="text-green-200 mt-1 relative z-10">Kelola data sekolah MI Annajiyah dari sini.</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @php
    $stats = [
        ['icon' => 'fa-user-graduate', 'value' => $totalSiswa, 'label' => 'Total Siswa', 'color' => 'from-blue-400 to-blue-600'],
        ['icon' => 'fa-chalkboard-user', 'value' => $totalGuru, 'label' => 'Total Guru', 'color' => 'from-green-400 to-green-600'],
        ['icon' => 'fa-calendar-alt', 'value' => $totalJadwal, 'label' => 'Jadwal', 'color' => 'from-purple-400 to-purple-600'],
        ['icon' => 'fa-clock', 'value' => $totalPendaftar, 'label' => 'Pending', 'color' => 'from-yellow-400 to-yellow-600'],
        ['icon' => 'fa-check-circle', 'value' => $totalDiterima, 'label' => 'Diterima', 'color' => 'from-emerald-400 to-emerald-600'],
        ['icon' => 'fa-calendar-check', 'value' => $totalKegiatan, 'label' => 'Kegiatan', 'color' => 'from-pink-400 to-pink-600'],
    ];
    @endphp
    @foreach($stats as $i => $stat)
    <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden cursor-pointer transition-all duration-300 hover:-translate-y-2 hover:shadow-xl animate-fade" style="animation-delay: {{ $i * 0.05 }}s">
        <div class="w-12 h-12 bg-gradient-to-br {{ $stat['color'] }} rounded-xl flex items-center justify-center mb-3">
            <i class="fas {{ $stat['icon'] }} text-white text-lg"></i>
        </div>
        <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $stat['value'] }}</h3>
        <p class="text-gray-500 text-xs mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
        <div>
            <h3 class="text-lg font-bold text-[var(--color-primary)] border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas fa-clipboard-check mr-2"></i>Ringkasan Verifikasi PPDB
            </h3>
            <p class="mt-2 text-xs font-bold {{ $ppdbSettings['is_open'] ? 'text-green-600' : 'text-red-600' }}">
                <i class="fas {{ $ppdbSettings['is_open'] ? 'fa-lock-open' : 'fa-lock' }} mr-1"></i>
                Pendaftaran publik {{ $ppdbSettings['is_open'] ? 'dibuka' : 'ditutup' }} untuk {{ $ppdbSettings['academic_year'] }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.konten', ['tab' => 'ppdb']) }}" class="inline-flex items-center justify-center gap-2 border-2 border-[var(--color-primary)] text-[var(--color-primary)] px-4 py-2 rounded-xl text-sm font-semibold hover:bg-green-50 transition">
                <i class="fas fa-cog"></i> Atur Status
            </a>
            <a href="{{ route('admin.ppdb', ['status' => 'pending']) }}" class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[var(--color-primary-light)] transition">
                <i class="fas fa-arrow-right"></i> Kelola Pendaftar
            </a>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
        @foreach($statusOptions as $status => $label)
            @php
                $tone = \App\Helpers\PpdbHelper::statusTone($status);
                $statusClass = match ($tone) {
                    'green' => 'bg-green-50 text-green-700 border-green-100',
                    'red' => 'bg-red-50 text-red-700 border-red-100',
                    'blue' => 'bg-blue-50 text-blue-700 border-blue-100',
                    'orange' => 'bg-orange-50 text-orange-700 border-orange-100',
                    default => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                };
            @endphp
            <a href="{{ route('admin.ppdb', ['status' => $status]) }}" class="border rounded-2xl p-4 {{ $statusClass }} hover:shadow-sm transition">
                <p class="text-2xl font-black">{{ $ppdbStatusCounts[$status] ?? 0 }}</p>
                <p class="text-xs font-bold mt-1">{{ $label }}</p>
            </a>
        @endforeach
    </div>
    <div class="border-t border-gray-100 pt-4">
        <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Perlu Tindak Lanjut</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            @forelse($perluTindakLanjut as $p)
                <a href="{{ route('admin.ppdb', ['q' => $p->nomor_pendaftaran]) }}" class="p-3 rounded-2xl border border-gray-100 hover:border-[var(--color-accent)] transition">
                    <p class="font-bold text-sm text-gray-800 truncate">{{ $p->nama }}</p>
                    <p class="text-[10px] text-gray-400 font-bold mt-1">{{ $p->nomor_pendaftaran }}</p>
                    <p class="text-[10px] text-[var(--color-primary)] font-black uppercase mt-2">{{ \App\Helpers\PpdbHelper::statusLabel($p->status_ppdb) }}</p>
                </a>
            @empty
                <p class="md:col-span-2 lg:col-span-5 text-sm text-gray-400">Tidak ada pendaftar yang perlu ditindaklanjuti saat ini.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm animate-fade">
        <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
            <i class="fas fa-chart-line mr-2"></i>Pendaftaran 7 Hari Terakhir
        </h3>
        @php
            $chartPoints = collect($chartLabels)->map(fn ($label, $index) => [
                'label' => $label,
                'value' => (int) ($chartData[$index] ?? 0),
            ]);
            $chartPeak = (int) $chartPoints->max('value');
            $chartScaleMax = max(1, $chartPeak);
            $chartLeft = 32;
            $chartRight = 668;
            $chartTop = 24;
            $chartBottom = 158;
            $chartStep = $chartPoints->count() > 1 ? ($chartRight - $chartLeft) / ($chartPoints->count() - 1) : 0;
            $linePoints = $chartPoints->values()->map(function ($point, $index) use ($chartLeft, $chartTop, $chartBottom, $chartStep, $chartScaleMax) {
                $x = $chartLeft + ($index * $chartStep);
                $ratio = $point['value'] / $chartScaleMax;
                $y = $chartBottom - ($ratio * ($chartBottom - $chartTop));

                return [
                    'label' => $point['label'],
                    'value' => $point['value'],
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                ];
            });
            $linePath = $linePoints->map(fn ($point) => $point['x'] . ',' . $point['y'])->implode(' ');
            $areaPath = $linePoints->isNotEmpty()
                ? 'M ' . $linePoints->first()['x'] . ' ' . $chartBottom . ' L ' . $linePoints->map(fn ($point) => $point['x'] . ' ' . $point['y'])->implode(' L ') . ' L ' . $linePoints->last()['x'] . ' ' . $chartBottom . ' Z'
                : '';
        @endphp
        <div class="h-56 rounded-2xl border border-gray-100 bg-gradient-to-b from-gray-50 to-white p-3">
            <svg viewBox="0 0 700 190" class="w-full h-full" role="img" aria-label="Diagram garis pendaftaran 7 hari terakhir">
                <defs>
                    <linearGradient id="registrationLineFill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="var(--color-accent)" stop-opacity="0.30" />
                        <stop offset="100%" stop-color="var(--color-primary)" stop-opacity="0.02" />
                    </linearGradient>
                </defs>

                @foreach([24, 57.5, 91, 124.5, 158] as $gridY)
                    <line x1="{{ $chartLeft }}" y1="{{ $gridY }}" x2="{{ $chartRight }}" y2="{{ $gridY }}" stroke="#e5e7eb" stroke-width="1" stroke-dasharray="4 8" />
                @endforeach

                @if($areaPath)
                    <path d="{{ $areaPath }}" fill="url(#registrationLineFill)" />
                @endif

                <polyline points="{{ $linePath }}"
                          fill="none"
                          stroke="var(--color-primary)"
                          stroke-width="4"
                          stroke-linecap="round"
                          stroke-linejoin="round" />

                @foreach($linePoints as $point)
                    <g>
                        <line x1="{{ $point['x'] }}" y1="{{ $chartBottom }}" x2="{{ $point['x'] }}" y2="166" stroke="#e5e7eb" stroke-width="1" />
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="7" fill="white" stroke="var(--color-accent)" stroke-width="4">
                            <title>{{ $point['label'] }}: {{ $point['value'] }} pendaftar</title>
                        </circle>
                        <text x="{{ $point['x'] }}" y="{{ max(12, $point['y'] - 14) }}" text-anchor="middle" class="fill-[var(--color-primary)] text-[11px] font-black">{{ $point['value'] }}</text>
                        <text x="{{ $point['x'] }}" y="184" text-anchor="middle" class="fill-gray-400 text-[10px] font-bold">{{ $point['label'] }}</text>
                    </g>
                @endforeach
            </svg>
        </div>
        <div class="flex justify-between mt-3 text-xs text-gray-400">
            <span>Total 7 hari: <strong class="text-[var(--color-primary)]">{{ $chartPoints->sum('value') }}</strong></span>
            <span>Puncak: <strong class="text-[var(--color-primary)]">{{ $chartPeak }}</strong></span>
        </div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade" style="animation-delay: 0.1s">
        <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-blue-500 pl-3">
            <i class="fas fa-calendar-check mr-2 text-blue-500"></i>Jadwal Hari Ini
        </h3>
        <div class="space-y-4">
            @forelse($jadwalHariIni as $j)
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 group hover:border-blue-200 transition-all">
                <div class="flex justify-between items-start mb-1">
                    <p class="font-bold text-sm text-gray-800">{{ $j->mapel }}</p>
                    <span class="text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-black">{{ $j->kelas }}</span>
                </div>
                <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fas fa-user text-[10px]"></i> {{ $j->guru?->nama }}</p>
                <p class="text-[10px] text-blue-600 mt-2 font-bold flex items-center gap-1">
                    <i class="fas fa-clock text-[9px]"></i> {{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}
                </p>
            </div>
            @empty
            <div class="py-10 text-center text-gray-300">
                <i class="fas fa-bed text-4xl mb-2 opacity-20"></i>
                <p class="text-xs font-bold">Tidak ada jadwal hari ini.</p>
            </div>
            @endforelse
            @if($jadwalHariIni->isNotEmpty())
            <a href="{{ route('admin.jadwal') }}" class="block text-center text-[10px] font-black text-blue-500 hover:text-blue-700 uppercase tracking-widest mt-4">Lihat Semua Jadwal <i class="fas fa-arrow-right ml-1"></i></a>
            @endif
        </div>
    </div>
</div>
@endsection
