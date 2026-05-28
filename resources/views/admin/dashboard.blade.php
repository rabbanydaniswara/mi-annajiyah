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
        <h3 class="text-lg font-bold text-[var(--color-primary)] border-l-4 border-[var(--color-accent)] pl-3">
            <i class="fas fa-clipboard-check mr-2"></i>Ringkasan Verifikasi PPDB
        </h3>
        <a href="{{ route('admin.ppdb', ['status' => 'pending']) }}" class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[var(--color-primary-light)] transition">
            <i class="fas fa-arrow-right"></i> Buka PPDB
        </a>
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
        <canvas id="chartPendaftar" height="150"></canvas>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartPendaftar'), {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Pendaftar',
            data: @json($chartData),
            borderColor: '#0b3b1e',
            backgroundColor: 'rgba(11,59,30,0.1)',
            tension: 0.4, fill: true, pointRadius: 5,
            pointBackgroundColor: '#f9c74f', pointBorderColor: '#0b3b1e',
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endpush
