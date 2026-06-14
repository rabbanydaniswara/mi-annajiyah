@extends('layouts.public')
@section('title', 'Pendaftaran PPDB - MI Annajiyah')
@section('meta_description', 'Formulir Pendaftaran Peserta Didik Baru (PPDB) MI Annajiyah Tahun Ajaran ' . ($ppdbTahunAjaran ?? '2026/2027') . '.')

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-light)] pt-32 pb-16 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="inline-block bg-[var(--color-accent)]/20 text-[var(--color-accent)] px-4 py-1 rounded-full text-sm font-semibold mb-4 border border-[var(--color-accent)]/30">
            PPDB {{ $ppdbTahunAjaran ?? date('Y').'/'.(date('Y') + 1) }}
        </span>
        <h1 class="text-3xl md:text-5xl font-black text-white mb-3">{{ $ppdbOpen ? 'Formulir Pendaftaran' : 'Pendaftaran Ditutup' }}</h1>
        <p class="text-green-200 text-lg">{{ $ppdbOpen ? 'Lengkapi 3 langkah pendaftaran untuk mendaftarkan putra/putri Anda' : 'Informasi pendaftaran peserta didik baru MI Annajiyah' }}</p>
    </div>
</div>

{{-- Form Section --}}
<section class="py-16 bg-gray-50" @if($ppdbOpen) x-data="registrationWizard()" @endif>
    <div class="max-w-3xl mx-auto px-4">
        @if($ppdbOpen)

        {{-- Progress Bar --}}
        <div class="mb-12 relative">
            <div class="flex justify-between items-center relative z-10">
                <template x-for="s in [1, 2, 3]" :key="s">
                    <div class="flex flex-col items-center">
                        <div :class="step >= s ? 'bg-[var(--color-primary)] text-white' : 'bg-white text-gray-400 border-2 border-gray-200'"
                             class="w-12 h-12 rounded-2xl flex items-center justify-center font-black transition-all duration-500 shadow-lg"
                             x-text="s"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest mt-2" 
                              :class="step >= s ? 'text-[var(--color-primary)]' : 'text-gray-400'"
                              x-text="s == 1 ? 'Siswa' : (s == 2 ? 'Wali' : 'Dokumen')"></span>
                    </div>
                </template>
            </div>
            <div class="absolute top-6 left-0 w-full h-1 bg-gray-200 -z-0 rounded-full">
                <div class="h-full bg-[var(--color-primary)] transition-all duration-500 rounded-full" 
                     :style="'width: ' + ((step - 1) / 2 * 100) + '%'"></div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-green-900/10 overflow-hidden">
            <form id="formPendaftaran" novalidate @@submit.prevent="submitForm" @@input="clearFieldError($event.target.name)">
                @csrf
                <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
                
                {{-- STEP 1: Data Siswa --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="p-8 md:p-12">
                    <h3 class="text-2xl font-black text-[var(--color-primary)] mb-8 flex items-center gap-3">
                        <span class="w-2 h-8 bg-[var(--color-accent)] rounded-full"></span>
                        Data Calon Siswa
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Lengkap Anak *</label>
                            <input type="text" name="nama" required minlength="3" maxlength="100" autocomplete="name" data-rule="person-name" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nama lengkap sesuai akte">
                            <p class="mt-1.5 text-xs text-gray-400">Hanya huruf, spasi, titik, apostrof, dan tanda hubung.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tempat Lahir *</label>
                            <input type="text" name="tempat_lahir" required minlength="2" maxlength="100" autocomplete="address-level2" data-rule="place" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Contoh: Jakarta">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" required max="{{ now()->subDay()->toDateString() }}" autocomplete="bday" data-rule="birth-date" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" required autocomplete="sex" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition appearance-none">
                                <option value="">Pilih...</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Asal Sekolah (TK/PAUD) *</label>
                            <input type="text" name="asal_sekolah" required minlength="2" maxlength="150" data-rule="school" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nama sekolah asal">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">NISN (Jika Ada)</label>
                            <input type="text" name="nisn" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" autocomplete="off" data-rule="nisn" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="10 digit angka">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">NIS (Jika Ada)</label>
                            <input type="text" name="nis" minlength="3" maxlength="20" pattern="[A-Za-z0-9./-]+" autocomplete="off" data-rule="nis" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Contoh: NIS-001">
                        </div>
                    </div>
                </div>

                {{-- STEP 2: Data Orang Tua --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="p-8 md:p-12">
                    <h3 class="text-2xl font-black text-[var(--color-primary)] mb-8 flex items-center gap-3">
                        <span class="w-2 h-8 bg-[var(--color-accent)] rounded-full"></span>
                        Data Orang Tua & Alamat
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Orang Tua / Wali *</label>
                            <input type="text" name="ortu" required minlength="3" maxlength="100" autocomplete="name" data-rule="person-name" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nama Ayah / Ibu / Wali">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. WhatsApp *</label>
                            <input type="tel" name="wa" required minlength="10" maxlength="30" inputmode="tel" autocomplete="tel" data-rule="whatsapp" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="081234567890 atau 6281234567890">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. Akte Kelahiran *</label>
                            <input type="text" name="akte" required minlength="3" maxlength="50" pattern="[A-Za-z0-9 ./-]+" autocomplete="off" data-rule="document-number" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Sesuai dokumen">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. Kartu Keluarga (KK) *</label>
                            <input type="text" name="kk" required inputmode="numeric" minlength="16" maxlength="16" pattern="[0-9]{16}" autocomplete="off" data-rule="kk" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="16 digit nomor KK">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alamat Lengkap *</label>
                            <textarea name="alamat" required minlength="10" maxlength="1000" rows="3" autocomplete="street-address" data-rule="address" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition resize-none" placeholder="Alamat lengkap domisili"></textarea>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Dokumen --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="p-8 md:p-12">
                    <h3 class="text-2xl font-black text-[var(--color-primary)] mb-8 flex items-center gap-3">
                        <span class="w-2 h-8 bg-[var(--color-accent)] rounded-full"></span>
                        Unggah Berkas Dokumen
                    </h3>
                    <p class="text-xs text-gray-400 mb-8 -mt-4 italic">Pastikan file jelas terbaca. Format: JPG, PNG, atau PDF (Max 5MB per file).</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File Akte Kelahiran *</label>
                            <input type="file" name="file_akte" accept=".jpg,.jpeg,.png,.pdf" required data-rule="document-file" @@change="updateFilePreview($event, 'file_akte')" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                            <p x-show="filePreviews.file_akte" x-text="filePreviews.file_akte" class="mt-3 text-xs font-semibold text-[var(--color-primary)]"></p>
                        </div>
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File Kartu Keluarga (KK) *</label>
                            <input type="file" name="file_kk" accept=".jpg,.jpeg,.png,.pdf" required data-rule="document-file" @@change="updateFilePreview($event, 'file_kk')" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                            <p x-show="filePreviews.file_kk" x-text="filePreviews.file_kk" class="mt-3 text-xs font-semibold text-[var(--color-primary)]"></p>
                        </div>
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File KTP Orang Tua / Wali *</label>
                            <input type="file" name="file_ktp" accept=".jpg,.jpeg,.png,.pdf" required data-rule="document-file" @@change="updateFilePreview($event, 'file_ktp')" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                            <p x-show="filePreviews.file_ktp" x-text="filePreviews.file_ktp" class="mt-3 text-xs font-semibold text-[var(--color-primary)]"></p>
                        </div>
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File Ijazah TK (Opsional)</label>
                            <input type="file" name="file_ijazah" accept=".jpg,.jpeg,.png,.pdf" data-rule="document-file" @@change="updateFilePreview($event, 'file_ijazah')" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                            <p x-show="filePreviews.file_ijazah" x-text="filePreviews.file_ijazah" class="mt-3 text-xs font-semibold text-[var(--color-primary)]"></p>
                        </div>
                    </div>
                </div>

                {{-- Feedback Message --}}
                <div id="formMessage" class="mx-8 md:mx-12 mb-6 hidden"></div>

                {{-- Navigation --}}
                <div class="bg-gray-50 p-8 md:p-12 flex justify-between gap-4">
                    <button type="button" x-show="step > 1" @@click="step--" class="px-8 py-4 bg-white text-gray-600 border-2 border-gray-200 rounded-2xl font-black text-sm hover:bg-gray-100 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Sebelumnya
                    </button>
                    
                    <button type="button" x-show="step < 3" @@click="nextStep()" class="ml-auto px-10 py-4 bg-[var(--color-primary)] text-white rounded-2xl font-black text-sm hover:bg-[var(--color-primary-light)] transition shadow-xl shadow-green-900/20 flex items-center gap-2">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>

                    <button type="submit" x-show="step === 3" :disabled="loading" class="ml-auto px-10 py-4 bg-green-600 text-white rounded-2xl font-black text-sm hover:bg-green-700 transition shadow-xl shadow-green-600/30 flex items-center gap-2">
                        <span x-show="!loading" class="flex items-center gap-2">Kirim Pendaftaran <i class="fas fa-paper-plane"></i></span>
                        <span x-show="loading" class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Mengirim...</span>
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-green-900/10 p-8 md:p-12 text-center border border-red-100">
            <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-lock text-3xl"></i>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-red-100 text-red-700 px-4 py-1.5 text-xs font-black uppercase tracking-widest mb-4">
                PPDB {{ $ppdbTahunAjaran }} Ditutup
            </span>
            <h2 class="text-2xl md:text-3xl font-black text-[var(--color-primary)] mb-4">Formulir Belum Dapat Diisi</h2>
            <p class="text-gray-600 leading-relaxed max-w-xl mx-auto whitespace-pre-line">{{ $ppdbClosedMessage }}</p>
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[var(--color-primary)] text-white font-bold hover:bg-[var(--color-primary-light)] transition">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('cek-pendaftaran') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-[var(--color-primary)] text-[var(--color-primary)] font-bold hover:bg-green-50 transition">
                    <i class="fas fa-search"></i> Cek Status Pendaftaran
                </a>
            </div>
        </div>
        @endif
        <p class="text-center text-gray-400 text-xs mt-8">Butuh bantuan? Hubungi panitia PPDB melalui WhatsApp yang tertera di website.</p>
    </div>
</section>
@endsection

@if($ppdbOpen)
@push('scripts')
<script>
function registrationWizard() {
    return {
        step: 1,
        loading: false,
        filePreviews: {},
        fieldSteps: {
            nama: 1,
            tempat_lahir: 1,
            tanggal_lahir: 1,
            jenis_kelamin: 1,
            asal_sekolah: 1,
            nisn: 1,
            nis: 1,
            ortu: 2,
            wa: 2,
            akte: 2,
            kk: 2,
            alamat: 2,
            file_akte: 3,
            file_kk: 3,
            file_ktp: 3,
            file_ijazah: 3,
        },
        fieldLabels: {
            nama: 'Nama lengkap anak',
            tempat_lahir: 'Tempat lahir',
            tanggal_lahir: 'Tanggal lahir',
            jenis_kelamin: 'Jenis kelamin',
            asal_sekolah: 'Asal sekolah',
            nisn: 'NISN',
            nis: 'NIS',
            ortu: 'Nama orang tua/wali',
            wa: 'Nomor WhatsApp',
            akte: 'Nomor akte kelahiran',
            kk: 'Nomor Kartu Keluarga',
            alamat: 'Alamat lengkap',
            file_akte: 'File akte kelahiran',
            file_kk: 'File Kartu Keluarga',
            file_ktp: 'File KTP orang tua/wali',
            file_ijazah: 'File ijazah',
        },
        notify(message, type = 'error') {
            if (typeof window.toast === 'function') {
                window.toast(message, type);
            }
        },
        formatFileSize(bytes) {
            if (bytes < 1024 * 1024) {
                return Math.max(1, Math.round(bytes / 1024)) + ' KB';
            }

            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },
        updateFilePreview(event, key) {
            const file = event.target.files[0];
            this.filePreviews[key] = file ? `${file.name} (${this.formatFileSize(file.size)})` : '';
            this.validateControl(event.target);
        },
        clearFieldError(name) {
            if (!name || name === 'website') return;

            const field = document.querySelector(`#formPendaftaran [name="${CSS.escape(name)}"]`);
            const error = document.querySelector(`#formPendaftaran [data-field-error="${CSS.escape(name)}"]`);

            if (field) {
                field.setCustomValidity('');
                field.classList.remove('border-red-500', 'bg-red-50');
            }
            if (error) {
                error.textContent = '';
                error.classList.add('hidden');
            }
        },
        showFieldError(field, message) {
            if (!field?.name) return;

            let error = document.querySelector(`#formPendaftaran [data-field-error="${CSS.escape(field.name)}"]`);
            if (!error) {
                error = document.createElement('p');
                error.dataset.fieldError = field.name;
                error.className = 'mt-2 text-xs font-semibold text-red-600';
                field.insertAdjacentElement('afterend', error);
            }

            error.textContent = message;
            error.classList.remove('hidden');
            field.classList.add('border-red-500', 'bg-red-50');
        },
        validationMessage(field) {
            const value = field.value.trim();
            const label = this.fieldLabels[field.name] || 'Field ini';

            if (field.required && !value && field.type !== 'file') {
                return `${label} wajib diisi.`;
            }
            if (field.required && field.type === 'file' && !field.files.length) {
                return `${label} wajib dipilih.`;
            }
            if (!value && field.type !== 'file') return '';

            const rule = field.dataset.rule;
            if (rule === 'nisn' && value && !/^\d{10}$/.test(value)) {
                return 'NISN harus terdiri dari tepat 10 digit angka.';
            }
            if (rule === 'kk' && !/^\d{16}$/.test(value)) {
                return 'Nomor Kartu Keluarga harus terdiri dari tepat 16 digit angka.';
            }
            if (field.minLength > 0 && value.length < field.minLength) {
                return `${label} minimal ${field.minLength} karakter.`;
            }
            if (field.maxLength > 0 && value.length > field.maxLength) {
                return `${label} maksimal ${field.maxLength} karakter.`;
            }

            const patterns = {
                'person-name': /^[\p{L}\p{M} .'\u2019-]+$/u,
                place: /^[\p{L}\p{M} .,'\u2019()/-]+$/u,
                school: /^[\p{L}\p{M}\p{N} .,'\u2019&()/-]+$/u,
                nis: /^[A-Za-z0-9./-]+$/,
                'document-number': /^[A-Za-z0-9 ./-]+$/,
                address: /^[\p{L}\p{M}\p{N}\s.,'\u2019#&()\/:-]+$/u,
            };

            if (patterns[rule] && !patterns[rule].test(value)) {
                const messages = {
                    'person-name': `${label} hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.`,
                    place: `${label} hanya boleh berisi huruf dan tanda baca yang wajar.`,
                    school: `${label} mengandung karakter yang tidak diizinkan.`,
                    nis: 'NIS hanya boleh berisi huruf, angka, titik, garis miring, dan tanda hubung.',
                    'document-number': 'Nomor akte hanya boleh berisi huruf, angka, spasi, titik, garis miring, dan tanda hubung.',
                    address: 'Alamat mengandung karakter yang tidak diizinkan.',
                };

                return messages[rule];
            }
            if (rule === 'whatsapp') {
                const number = value.replace(/[^\d+]/g, '').replace(/^\+/, '');
                if (!/^(?:08\d{8,11}|628\d{8,11})$/.test(number)) {
                    return 'Nomor WhatsApp harus memakai format Indonesia yang valid, contoh: 081234567890.';
                }
            }
            if (rule === 'birth-date' && value >= '{{ now()->toDateString() }}') {
                return 'Tanggal lahir harus berupa tanggal sebelum hari ini.';
            }
            if (rule === 'document-file' && field.files.length) {
                const file = field.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    return `${label} harus berformat JPG, JPEG, PNG, atau PDF.`;
                }
                if (file.size > 5 * 1024 * 1024) {
                    return `${label} maksimal 5 MB.`;
                }
            }

            return field.checkValidity() ? '' : `${label} belum sesuai format yang diminta.`;
        },
        validateControl(field) {
            if (!field?.name || field.name === 'website') return true;

            this.clearFieldError(field.name);
            const message = this.validationMessage(field);
            if (!message) return true;

            field.setCustomValidity(message);
            this.showFieldError(field, message);

            return false;
        },
        validateFields(fields) {
            let firstInvalid = null;

            fields.forEach(field => {
                if (!this.validateControl(field) && !firstInvalid) {
                    firstInvalid = field;
                }
            });

            if (firstInvalid) {
                firstInvalid.focus({ preventScroll: true });
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                this.notify('Periksa kembali data yang belum valid.');

                return false;
            }

            return true;
        },
        handleServerErrors(errors) {
            const entries = Object.entries(errors || {});
            if (!entries.length) return;

            let firstField = null;
            let firstStep = 3;

            entries.forEach(([name, messages]) => {
                const field = document.querySelector(`#formPendaftaran [name="${CSS.escape(name)}"]`);
                if (!field) return;

                const message = Array.isArray(messages) ? messages[0] : String(messages);
                field.setCustomValidity(message);
                this.showFieldError(field, message);

                if (!firstField || (this.fieldSteps[name] || 3) < firstStep) {
                    firstField = field;
                    firstStep = this.fieldSteps[name] || 3;
                }
            });

            this.step = firstStep;
            this.$nextTick(() => {
                firstField?.focus({ preventScroll: true });
                firstField?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        },
        nextStep() {
            const currentFields = document.querySelectorAll(
                `#formPendaftaran div[x-show='step === ${this.step}'] input:not([type="hidden"]), ` +
                `#formPendaftaran div[x-show='step === ${this.step}'] select, ` +
                `#formPendaftaran div[x-show='step === ${this.step}'] textarea`
            );

            if (this.validateFields(currentFields)) {
                this.step++;
                window.scrollTo({top: 200, behavior: 'smooth'});
            }
        },
        async submitForm() {
            const allFields = document.querySelectorAll(
                '#formPendaftaran input:not([type="hidden"]), #formPendaftaran select, #formPendaftaran textarea'
            );
            let firstInvalidStep = null;

            allFields.forEach(field => {
                if (!this.validateControl(field) && firstInvalidStep === null) {
                    firstInvalidStep = this.fieldSteps[field.name] || 1;
                }
            });

            if (firstInvalidStep !== null) {
                this.step = firstInvalidStep;
                this.$nextTick(() => {
                    const field = Array.from(allFields).find(item => !item.checkValidity());
                    field?.focus({ preventScroll: true });
                    field?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
                this.notify('Periksa kembali data yang belum valid.');
                return;
            }

            this.loading = true;
            const formEl = document.getElementById('formPendaftaran');
            const formData = new FormData(formEl);
            const msgDiv = document.getElementById('formMessage');
            
            try {
                const res = await fetch('{{ route("api.pendaftaran") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (res.status === 422) {
                    this.handleServerErrors(data.errors);
                    this.notify(data.message || 'Periksa kembali data pendaftaran.');
                    this.loading = false;
                    return;
                }
                
                msgDiv.classList.remove('hidden');
                if (data.success) {
                    msgDiv.className = 'p-5 rounded-3xl bg-green-50 text-green-800 font-bold border-2 border-green-200 animate-fade';
                    msgDiv.replaceChildren();

                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex flex-col md:flex-row items-center gap-5';

                    const icon = document.createElement('i');
                    icon.className = 'fas fa-check-circle text-4xl';

                    const textWrap = document.createElement('div');
                    textWrap.className = 'flex-1 text-center md:text-left';
                    const title = document.createElement('p');
                    title.className = 'text-xl';
                    title.textContent = 'Pendaftaran Berhasil!';
                    const message = document.createElement('p');
                    message.className = 'text-sm font-normal';
                    message.textContent = data.message || 'Pendaftaran berhasil.';
                    textWrap.append(title, message);

                    const printLink = document.createElement('a');
                    printLink.href = data.card_url || '#';
                    printLink.target = '_blank';
                    printLink.rel = 'noopener noreferrer';
                    printLink.className = 'px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-black hover:bg-green-700 transition flex items-center gap-2 shadow-lg';
                    printLink.innerHTML = '<i class="fas fa-print"></i> CETAK KARTU';

                    wrapper.append(icon, textWrap, printLink);
                    msgDiv.appendChild(wrapper);
                    formEl.reset();
                    this.filePreviews = {};
                    this.step = 1;
                    window.scrollTo({top: msgDiv.offsetTop - 100, behavior: 'smooth'});
                } else {
                    msgDiv.className = 'p-5 rounded-3xl bg-red-50 text-red-800 font-bold border-2 border-red-200 animate-fade';
                    msgDiv.replaceChildren();

                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-center gap-3';
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-times-circle text-2xl';
                    const textWrap = document.createElement('div');
                    const title = document.createElement('p');
                    title.className = 'text-lg';
                    title.textContent = 'Gagal Mengirim';
                    const message = document.createElement('p');
                    message.className = 'text-sm font-normal';
                    message.textContent = data.message || 'Pendaftaran gagal diproses.';
                    textWrap.append(title, message);
                    wrapper.append(icon, textWrap);
                    msgDiv.appendChild(wrapper);
                }
            } catch (e) {
                msgDiv.classList.remove('hidden');
                msgDiv.className = 'p-5 rounded-3xl bg-red-50 text-red-800 font-bold border-2 border-red-200 animate-fade';
                msgDiv.textContent = 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
            }
            this.loading = false;
        }
    };
}
</script>
@endpush
@endif
