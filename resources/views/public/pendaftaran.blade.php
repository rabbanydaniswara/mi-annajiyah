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
        <h1 class="text-3xl md:text-5xl font-black text-white mb-3">Formulir Pendaftaran</h1>
        <p class="text-green-200 text-lg">Lengkapi 3 langkah pendaftaran untuk mendaftarkan putra/putri Anda</p>
    </div>
</div>

{{-- Form Section --}}
<section class="py-16 bg-gray-50" x-data="registrationWizard()">
    <div class="max-w-3xl mx-auto px-4">

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
            <form id="formPendaftaran" @@submit.prevent="submitForm">
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
                            <input type="text" name="nama" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nama lengkap sesuai akte">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tempat Lahir *</label>
                            <input type="text" name="tempat_lahir" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Contoh: Jakarta">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition appearance-none">
                                <option value="">Pilih...</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Asal Sekolah (TK/PAUD) *</label>
                            <input type="text" name="asal_sekolah" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nama sekolah asal">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">NISN (Jika Ada)</label>
                            <input type="text" name="nisn" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="10 digit angka">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">NIS (Jika Ada)</label>
                            <input type="text" name="nis" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nomor induk siswa">
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
                            <input type="text" name="ortu" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Nama Ayah / Ibu / Wali">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. WhatsApp *</label>
                            <input type="text" name="wa" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. Akte Kelahiran *</label>
                            <input type="text" name="akte" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="Sesuai dokumen">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">No. Kartu Keluarga (KK) *</label>
                            <input type="text" name="kk" required class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition" placeholder="16 digit nomor KK">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Alamat Lengkap *</label>
                            <textarea name="alamat" required rows="3" class="form-input-premium w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-[var(--color-accent)] focus:bg-white outline-none transition resize-none" placeholder="Alamat lengkap domisili"></textarea>
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
                            <input type="file" name="file_akte" accept=".jpg,.jpeg,.png,.pdf" required class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                        </div>
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File Kartu Keluarga (KK) *</label>
                            <input type="file" name="file_kk" accept=".jpg,.jpeg,.png,.pdf" required class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                        </div>
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File KTP Orang Tua / Wali *</label>
                            <input type="file" name="file_ktp" accept=".jpg,.jpeg,.png,.pdf" required class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
                        </div>
                        <div class="p-5 rounded-3xl border-2 border-dashed border-gray-100 hover:border-[var(--color-accent)] transition">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">File Ijazah TK (Opsional)</label>
                            <input type="file" name="file_ijazah" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 file:font-bold hover:file:bg-[var(--color-accent)] transition-all">
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
        <p class="text-center text-gray-400 text-xs mt-8">Butuh bantuan? Hubungi panitia PPDB melalui WhatsApp yang tertera di website.</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
function registrationWizard() {
    return {
        step: 1,
        loading: false,
        nextStep() {
            // Basic validation for current step
            const currentFields = document.querySelector(`#formPendaftaran div[x-show='step === ${this.step}']`).querySelectorAll('[required]');
            let valid = true;
            currentFields.forEach(f => {
                if(!f.value) {
                    valid = false;
                    f.classList.add('border-red-500');
                    setTimeout(() => f.classList.remove('border-red-500'), 2000);
                }
            });
            
            if(valid) {
                this.step++;
                window.scrollTo({top: 200, behavior: 'smooth'});
            } else {
                window.toast('Harap lengkapi semua field wajib (*) sebelum lanjut.', 'error');
            }
        },
        async submitForm() {
            this.loading = true;
            const formEl = document.getElementById('formPendaftaran');
            const formData = new FormData(formEl);
            const msgDiv = document.getElementById('formMessage');
            
            try {
                const res = await fetch('{{ route("api.pendaftaran") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                
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
