@extends('layouts.admin')
@section('title', 'Kelola Admin')
@section('header_icon', 'user-shield')
@section('header_title', 'Manajemen Admin')

@section('content')
<div class="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-light)] rounded-2xl p-6 mb-6 text-white relative overflow-hidden animate-fade">
    <div class="absolute top-[-50%] right-[-20%] w-72 h-72 bg-white/10 rounded-full"></div>
    <h3 class="text-xl font-bold"><i class="fas fa-user-shield mr-2"></i>Kelola Data Admin</h3>
    <p class="text-green-200 mt-1">Kelola akun admin dan operator yang dapat mengakses sistem.</p>
</div>

<a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-[var(--color-accent)] text-[var(--color-primary)] px-5 py-2 rounded-xl font-semibold hover:bg-[var(--color-accent-dark)] transition mb-6 text-sm">
    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
</a>

<div x-data="{ tab: '{{ request('page') || request('tab') === 'logs' ? 'logs' : 'admins' }}' }">
    {{-- Tab Navigation --}}
    <div class="flex gap-1 bg-white p-1 rounded-2xl shadow-sm mb-6 w-fit border border-gray-100">
        <button @click="tab = 'admins'" 
                :class="tab === 'admins' ? 'bg-[var(--color-primary)] text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-users"></i> Daftar Admin
        </button>
        <button @click="tab = 'logs'" 
                :class="tab === 'logs' ? 'bg-[var(--color-primary)] text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 flex items-center gap-2">
            <i class="fas fa-history"></i> Log Aktivitas
        </button>
    </div>

    {{-- Tab Content: Admins --}}
    <div x-show="tab === 'admins'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4">
        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
                <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-users text-white text-lg"></i></div>
                <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $admins->count() }}</h3><p class="text-gray-500 text-xs">Total Admin</p>
            </div>
            <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
                <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-user-shield text-white text-lg"></i></div>
                <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $admins->where('role', 'admin')->count() }}</h3><p class="text-gray-500 text-xs">Admin</p>
            </div>
            <div class="stat-card-admin bg-white rounded-2xl p-5 relative overflow-hidden transition-all hover:-translate-y-2 hover:shadow-xl">
                <div class="w-12 h-12 bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-accent-dark)] rounded-xl flex items-center justify-center mb-2"><i class="fas fa-user-clock text-white text-lg"></i></div>
                <h3 class="text-2xl font-black text-[var(--color-primary)]">{{ $admins->where('role', 'operator')->count() }}</h3><p class="text-gray-500 text-xs">Operator</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6 animate-fade">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3">
                <i class="fas {{ $edit ? 'fa-edit' : 'fa-plus-circle' }} mr-2"></i>{{ $edit ? 'Edit Admin' : 'Tambah Admin Baru' }}
            </h3>
            <form method="POST" action="{{ route('admin.admin.store') }}">
                @csrf
                @if($edit)<input type="hidden" name="id" value="{{ $edit->id }}">@endif
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-user mr-1"></i>Username *</label>
                        <input type="text" name="username" value="{{ old('username', $edit->username ?? '') }}" required class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                        <small class="text-gray-400">Username harus unik</small>
                    </div>
                    <div>
                        <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-lock mr-1"></i>Password {{ $edit ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                        <input type="password" name="password" {{ $edit ? '' : 'required' }} class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                        <small class="text-gray-400">Minimal 8 karakter</small>
                    </div>
                    <div>
                        <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-check-double mr-1"></i>Konfirmasi Password {{ $edit ? '' : '*' }}</label>
                        <input type="password" name="password_confirmation" {{ $edit ? '' : 'required' }} class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="font-semibold text-sm text-[var(--color-primary)] block mb-1"><i class="fas fa-tag mr-1"></i>Role *</label>
                        <select name="role" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-[var(--color-accent)] outline-none transition text-sm">
                            <option value="admin" {{ old('role', $edit->role ?? '') === 'admin' ? 'selected' : '' }}>Admin (Akses penuh)</option>
                            <option value="operator" {{ old('role', $edit->role ?? '') === 'operator' ? 'selected' : '' }}>Operator (Akses terbatas)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="submit" class="bg-[var(--color-primary)] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[var(--color-primary-light)] transition text-sm"><i class="fas fa-save mr-1"></i> {{ $edit ? 'Update' : 'Simpan' }}</button>
                    @if($edit)<a href="{{ route('admin.admin') }}" class="bg-gray-500 text-white px-6 py-2 rounded-xl font-semibold hover:bg-gray-600 transition text-sm">Batal</a>@endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade overflow-x-auto">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-list mr-2"></i>Daftar Admin</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[var(--color-primary)] uppercase text-xs font-semibold">
                        <th class="p-3 text-left">ID</th><th class="p-3 text-left">Username</th><th class="p-3 text-left">Role</th><th class="p-3 text-left">Dibuat</th><th class="p-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $a)
                    <tr class="border-b border-gray-100 hover:bg-yellow-50/50 transition">
                        <td class="p-3">{{ $a->id }}</td>
                        <td class="p-3 font-semibold">
                            {{ $a->username }}
                            @if($a->username === 'admin')<span class="bg-[var(--color-accent)] text-[var(--color-primary)] px-2 py-0.5 rounded-full text-xs font-bold ml-2">Super</span>@endif
                        </td>
                        <td class="p-3"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold"><i class="fas {{ $a->role === 'admin' ? 'fa-user-shield' : 'fa-user-clock' }} mr-1"></i>{{ ucfirst($a->role) }}</span></td>
                        <td class="p-3 text-xs">{{ $a->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="p-3">
                            @php
                                $isCurrentUser = $a->is(Auth::user());
                                $isLastAdmin = $a->role === 'admin' && $admins->where('role', 'admin')->count() <= 1;
                            @endphp
                            @if($a->username !== 'admin')
                            <a href="{{ route('admin.admin', ['edit' => $a->id]) }}" class="bg-[var(--color-accent)] text-[var(--color-primary)] px-2 py-1 rounded text-xs font-semibold"><i class="fas fa-edit"></i></a>
                                @if(!$isCurrentUser && !$isLastAdmin)
                                <form method="POST" action="{{ route('admin.admin.destroy', $a->id) }}" class="inline"
                                      data-confirm="Yakin ingin menghapus admin {{ $a->username }}?"
                                      data-title="Hapus Akun Admin"
                                      data-button="Hapus"
                                      data-type="danger">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs"><i class="fas fa-trash"></i></button>
                                </form>
                                @else
                                <span class="text-gray-400 text-xs ml-1"><i class="fas fa-shield-alt mr-1"></i>Protected</span>
                                @endif
                            @else<span class="text-gray-400 text-xs"><i class="fas fa-lock mr-1"></i>Protected</span>@endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-10 text-gray-400"><i class="fas fa-inbox text-3xl mb-2 block"></i>Belum ada admin.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab Content: Logs --}}
    <div x-show="tab === 'logs'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" style="display: none;">
        <div class="bg-white rounded-2xl p-6 shadow-sm animate-fade overflow-x-auto">
            <h3 class="text-lg font-bold text-[var(--color-primary)] mb-4 border-l-4 border-[var(--color-accent)] pl-3"><i class="fas fa-history mr-2"></i>Log Aktivitas Sistem</h3>
            <form method="GET" action="{{ route('admin.admin') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-5">
                <input type="hidden" name="tab" value="logs">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">User</label>
                    <select name="log_user" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">Semua</option>
                        <option value="system" {{ request('log_user') === 'system' ? 'selected' : '' }}>System</option>
                        @foreach($usersForFilter as $user)
                            <option value="{{ $user->id }}" {{ (string) request('log_user') === (string) $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Action</label>
                    <select name="log_action" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">Semua</option>
                        @foreach($actionList as $action)
                            <option value="{{ $action }}" {{ request('log_action') === $action ? 'selected' : '' }}>{{ str_replace('_', ' ', $action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Model</label>
                    <select name="log_model" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="">Semua</option>
                        @foreach($modelList as $model)
                            <option value="{{ $model }}" {{ request('log_model') === $model ? 'selected' : '' }}>{{ $model }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Dari</label>
                    <input type="date" name="log_dari" value="{{ request('log_dari') }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Sampai</label>
                    <input type="date" name="log_sampai" value="{{ request('log_sampai') }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-[var(--color-primary)] text-white rounded-xl text-sm font-semibold"><i class="fas fa-filter"></i></button>
                    <a href="{{ route('admin.admin', ['tab' => 'logs']) }}" class="px-4 py-2 bg-gray-100 text-gray-500 rounded-xl text-sm font-semibold"><i class="fas fa-rotate-left"></i></a>
                </div>
            </form>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[var(--color-primary)] uppercase text-xs font-semibold">
                        <th class="p-3 text-left">Waktu</th>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Tindakan</th>
                        <th class="p-3 text-left">Deskripsi</th>
                        <th class="p-3 text-left">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-100 hover:bg-blue-50/50 transition">
                        <td class="p-3 whitespace-nowrap text-gray-500 font-medium">
                            {{ $log->created_at->translatedFormat('d/m/Y H:i') }}
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center text-[10px] font-black uppercase">
                                    {{ substr($log->user->username ?? '?', 0, 1) }}
                                </div>
                                <span class="font-bold text-gray-700">{{ $log->user->username ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                @if(str_contains($log->action, 'delete')) bg-red-100 text-red-600
                                @elseif(str_contains($log->action, 'create')) bg-green-100 text-green-600
                                @else bg-blue-100 text-blue-600 @endif">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-600">
                            {{ $log->description }}
                        </td>
                        <td class="p-3 whitespace-nowrap text-xs font-mono text-gray-400">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-400 italic">Belum ada log aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($logs->hasPages())
            <div class="mt-6 pt-6 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
