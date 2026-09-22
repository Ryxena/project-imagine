@extends('layout.admin.AdminDashboardLayout')

@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi data diri dan keamanan akun Anda.')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 animate-in">

        <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-6 lg:p-8">
            <div class="flex items-center justify-between pb-4 border-b border-cream-100">
                <div>
                    <h3 class="text-base font-semibold text-cream-900">Informasi Pribadi</h3>
                    <p class="text-xs text-cream-500 mt-0.5">Perbarui foto profil, nama, dan nomor telepon Anda.</p>
                </div>
                <span class="bg-sage-100 text-sage-700 text-xs font-semibold px-3 py-1 rounded-full">Owner</span>
            </div>

            <form id="form-info" class="mt-6 space-y-6">
                <div class="flex items-center gap-5">
                    <div class="relative group w-20 h-20 shrink-0">
                        @if ($user->image)
                            <img id="avatar-preview" src="{{ asset('storage/' . $user->image) }}"
                                class="w-20 h-20 rounded-2xl object-cover border-2 border-cream-200 shadow-sm">
                            <div id="avatar-fallback"
                                class="hidden w-20 h-20 rounded-2xl bg-sage-200 text-sage-800 text-2xl font-bold items-center justify-center">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @else
                            <img id="avatar-preview" src=""
                                class="hidden w-20 h-20 rounded-2xl object-cover border-2 border-cream-200 shadow-sm">
                            <div id="avatar-fallback"
                                class="w-20 h-20 rounded-2xl bg-sage-200 text-sage-800 text-2xl font-bold flex items-center justify-center">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <label for="edit-image"
                            class="absolute inset-0 bg-black/50 text-white rounded-2xl opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center cursor-pointer transition-opacity duration-200 text-xs font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5 mb-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                            </svg>
                            Ubah
                        </label>

                        <input type="file" id="edit-image" accept="image/png, image/jpeg, image/jpg, image/webp"
                            class="hidden" onchange="previewImage(event)">
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-cream-900">Foto Profil</h4>
                        <p class="text-xs text-cream-500 mt-0.5">Format: JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-cream-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" id="edit-name" value="{{ $user->name }}"
                            class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-cream-700 mb-1.5">Nomor Telepon</label>
                        <input type="text" id="edit-no-hp" value="{{ $user->no_hp }}"
                            class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-cream-700 mb-1.5">Email Akun (Read-only)</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-cream-200 bg-cream-100 text-cream-500 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <p id="info-error" class="hidden text-xs text-terracotta-600 mb-2"></p>
                    <p id="info-success" class="hidden text-xs text-sage-700 mb-2"></p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-cream-100">
                    <p class="text-xs text-cream-400">Bergabung sejak {{ $user->created_at->translatedFormat('d F Y') }}</p>
                    <button type="button" onclick="submitInfo()"
                        class="bg-sage-700 text-white text-sm font-medium px-6 py-2.5 rounded-full hover:bg-sage-800 active:scale-[0.98] transition-all duration-150 ease-out shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-6 lg:p-8">
            <div class="pb-4 border-b border-cream-100">
                <h3 class="text-base font-semibold text-cream-900">Keamanan Akun</h3>
                <p class="text-xs text-cream-500 mt-0.5">Pastikan akun Anda menggunakan password yang aman.</p>
            </div>

            <form id="form-password" class="mt-6 space-y-4 max-w-xl">
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Password Lama</label>
                    <input type="password" id="current-password" placeholder="Masukkan password saat ini"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Password Baru</label>
                    <input type="password" id="new-password" placeholder="Masukkan password baru"
                        class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                    <p class="mt-1 text-[11px] text-cream-500">Minimal 8 karakter.</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-cream-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm-password" placeholder="Ulangi password baru"
                        class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-cream-300 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-sage-400 focus:border-sage-400 transition-colors duration-150">
                </div>
                <div id="password-error"
                    class="hidden flex items-start gap-2.5 p-3.5 bg-terracotta-50 border border-terracotta-200 rounded-xl animate-in slide-in-from-top-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-4 h-4 text-terracotta-600 shrink-0 mt-0.5">
                        <path fill-rule="evenodd"
                            d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <p id="password-error-text" class="text-xs font-semibold text-terracotta-800 leading-relaxed"></p>
                </div>

                <div id="password-success"
                    class="hidden flex items-start gap-2.5 p-3.5 bg-sage-50 border border-sage-200 rounded-xl animate-in slide-in-from-top-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-4 h-4 text-sage-600 shrink-0 mt-0.5">
                        <path fill-rule="evenodd"
                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                            clip-rule="evenodd" />
                    </svg>
                    <p id="password-success-text" class="text-xs font-semibold text-sage-800 leading-relaxed"></p>
                </div>

                <div class="pt-2">
                    <button type="button" id="btn-submit-password" onclick="submitPassword()"
                        class="w-full flex items-center justify-center gap-2 bg-white text-sage-700 border border-sage-300 text-sm font-medium px-5 py-2.5 rounded-full hover:bg-sage-50 active:scale-[0.98] transition-all duration-150 ease-out">
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                    document.getElementById('avatar-preview').classList.remove('hidden');
                    document.getElementById('avatar-fallback').classList.add('hidden');
                    document.getElementById('avatar-fallback').classList.remove('flex');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function submitInfo() {
            const errorEl = document.getElementById('info-error');
            const successEl = document.getElementById('info-success');
            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');

            const name = document.getElementById('edit-name').value.trim();
            const noHp = document.getElementById('edit-no-hp').value.trim();
            const imageInput = document.getElementById('edit-image');

            if (!name || !noHp) {
                errorEl.textContent = 'Nama dan nomor telepon wajib diisi.';
                errorEl.classList.remove('hidden');
                return;
            }

            const formData = new FormData();
            formData.append('name', name);
            formData.append('no_hp', noHp);
            formData.append('_method', 'PUT');

            if (imageInput && imageInput.files.length > 0) {
                formData.append('image', imageInput.files[0]);
            }

            try {
                const res = await fetch("{{ route('admin.profile.update') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });

                const json = await res.json();

                if (!res.ok) {
                    errorEl.textContent = json.message || 'Gagal menyimpan perubahan.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                successEl.textContent = 'Perubahan berhasil disimpan.';
                successEl.classList.remove('hidden');
                setTimeout(() => window.location.reload(), 1000);
            } catch (err) {
                errorEl.textContent = 'Terjadi kesalahan jaringan.';
                errorEl.classList.remove('hidden');
            }
        }

        async function submitPassword() {
            const errorContainer = document.getElementById('password-error');
            const errorText = document.getElementById('password-error-text');
            const successContainer = document.getElementById('password-success');
            const successText = document.getElementById('password-success-text');

            const submitBtn = document.getElementById('btn-submit-password');
            const originalBtnHtml = submitBtn.innerHTML;

            errorContainer.classList.add('hidden');
            successContainer.classList.add('hidden');

            const currentPassword = document.getElementById('current-password').value;
            const password = document.getElementById('new-password').value;
            const confirm = document.getElementById('confirm-password').value;

            if (!currentPassword) {
                errorText.textContent = 'Password lama wajib diisi untuk keamanan.';
                errorContainer.classList.remove('hidden');
                return;
            }
            if (!password || password.length < 8) {
                errorText.textContent = 'Password baru minimal 8 karakter.';
                errorContainer.classList.remove('hidden');
                return;
            }
            if (password !== confirm) {
                errorText.textContent = 'Konfirmasi password tidak cocok dengan password baru.';
                errorContainer.classList.remove('hidden');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            submitBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-sage-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;

            try {
                const res = await fetch("{{ route('admin.profile.update') }}", {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        password: password,
                        password_confirmation: confirm,
                    }),
                });

                const json = await res.json();

                if (!res.ok) {
                    let errorMsg = json.message || 'Gagal mengganti password. Silakan coba lagi.';
                    if (json.errors) {
                        if (json.errors.current_password) errorMsg = 'Password lama yang Anda masukkan salah.';
                        else if (json.errors.password) errorMsg = json.errors.password[0];
                    }

                    errorText.textContent = errorMsg;
                    errorContainer.classList.remove('hidden');
                } else {
                    successText.textContent = 'Password berhasil diganti! Pastikan Anda mengingatnya.';
                    successContainer.classList.remove('hidden');
                    document.getElementById('form-password').reset();
                }
            } catch (err) {
                errorText.textContent = 'Gagal terhubung ke server. Periksa koneksi internet Anda.';
                errorContainer.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                submitBtn.innerHTML = originalBtnHtml;
            }
        }
    </script>
@endpush
