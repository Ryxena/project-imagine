<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk | NgekostYuk</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-cream-page font-sans antialiased text-cream-900">

    <div class="min-h-screen grid lg:grid-cols-2">
        <section class="hidden lg:flex relative overflow-hidden bg-sage-800">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-sage-500/20 blur-[100px]">
                </div>
                <div
                    class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-sage-600/30 blur-[80px]">
                </div>
            </div>

            <div class="relative z-10 flex flex-col justify-center gap-8 h-full px-16 py-16 w-full max-w-2xl mx-auto">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-[32px] h-[32px] flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo NgekostYuk"
                                class="w-full h-full object-contain">
                        </div>
                        <h1 class="text-[25px] font-bold leading-none tracking-tight flex items-baseline">
                            <span class="text-white">Ngekost</span><span class="text-terracotta-400">Yuk</span>
                        </h1>
                    </div>

                    <h1 class="mt-5 text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Kelola kost,<br>
                        <span class="text-sage-300">lebih tenang setiap hari.</span>
                    </h1>

                    <p class="my-3 text-sage-100/90 leading-relaxed text-[15px] max-w-md">
                        Semua kebutuhan pengelolaan kost tersedia dalam satu aplikasi.
                    </p>
                </div>

                <div>
                    <div class="space-y-4">
                        <div
                            class="group flex items-center gap-4 p-2 pr-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm shadow-xl w-max hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 ease-out cursor-default">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-[12px] bg-sage-500/80 border border-sage-500/50 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform duration-300">
                                <span class="w-5 h-5 text-white flex items-center justify-center">
                                    @include('partial.icons.receipt')
                                </span>
                            </div>
                            <span class="text-[12px] text-sage-50 font-medium tracking-wide">
                                Tagihan dan pembayaran tercatat rapi
                            </span>
                        </div>
                        <div
                            class="group flex items-center gap-4 p-2 pr-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm shadow-xl w-max hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 ease-out cursor-default ml-4 lg:ml-8">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-[12px] bg-sage-500/80 border border-sage-500/50 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform duration-300">
                                <span class="w-5 h-5 text-white flex items-center justify-center">
                                    @include('partial.icons.megaphone')
                                </span>
                            </div>
                            <span class="text-[12px] text-sage-50 font-medium tracking-wide">
                                Kabar dan pengumuman langsung sampai
                            </span>
                        </div>
                        <div
                            class="group flex items-center gap-4 p-2 pr-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm shadow-xl w-max hover:bg-white/10 hover:-translate-y-1 transition-all duration-300 ease-out cursor-default">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-[12px] bg-sage-500/80 border border-sage-500/50 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform duration-300">
                                <span class="w-5 h-5 text-white flex items-center justify-center">
                                    @include('partial.icons.warning')
                                </span>
                            </div>
                            <span class="text-[12px] text-sage-50 font-medium tracking-wide">
                                Keluhan ditindaklanjuti dengan lebih teratur
                            </span>
                        </div>
                    </div>

                    <p class="mt-12 text-sm text-sage-400/80 font-medium tracking-wide">
                        © {{ date('Y') }} NgekostYuk
                    </p>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center px-6 py-10 lg:py-16 relative">

            <div class="w-full max-w-sm animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="lg:hidden text-center mb-10 flex flex-col items-center">
                    <div class="w-14 h-14 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo NgekostYuk"
                            class="w-full h-full object-contain">
                    </div>
                    <h1 class="mt-4 text-2xl font-bold leading-none tracking-tight flex items-baseline justify-center">
                        <span class="text-sage-900">Ngekost</span><span class="text-terracotta-500">Yuk</span>
                    </h1>
                    <p class="mt-1.5 text-sm font-medium text-cream-600 tracking-wide uppercase">Kelola Kost, Tanpa
                        Ribet</p>
                </div>

                <div>
                    <h2 class="text-2xl lg:text-3xl font-bold text-cream-900 tracking-tight">
                        Selamat Datang Kembali
                    </h2>
                    <p class="mt-1.5 text-[14px] tracking-tight text-cream-600 leading-relaxed sm:whitespace-nowrap">
                        Masuk ke akun Anda untuk melanjutkan aktivitas di NgekostYuk.
                    </p>
                </div>

                @if ($errors->any())
                    <div
                        class="mt-6 rounded-xl bg-terracotta-50 border border-terracotta-200 px-4 py-3 flex gap-3 items-start">
                        <svg class="w-5 h-5 text-terracotta-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-[13px] text-terracotta-700 font-medium">
                            {{ $errors->first() }}
                        </p>
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-1 text-[11px] font-bold text-cream-800 uppercase tracking-wider">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-cream-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>

                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="nama@email.com"
                                class="w-full rounded-lg border border-cream-300 bg-white pl-9 pr-3.5 py-2 text-[13px] shadow-sm transition-all duration-200
                                placeholder:text-cream-400
                                focus:border-sage-500 focus:ring-2 focus:ring-sage-500/20 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 text-[11px] font-bold text-cream-800 uppercase tracking-wider">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-cream-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>

                            <input id="password" type="password" name="password" required placeholder="••••••••"
                                class="w-full rounded-lg border border-cream-300 bg-white pl-9 pr-9 py-2 text-[13px] shadow-sm transition-all duration-200
                                placeholder:text-cream-400
                                focus:border-sage-500 focus:ring-2 focus:ring-sage-500/20 focus:outline-none">

                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-cream-400 hover:text-sage-600 transition-colors">
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full rounded-full bg-sage-700 py-2.5 mt-2 text-[13px] font-semibold text-white shadow-sm
                        transition-all duration-200 ease-out
                        hover:bg-sage-800 hover:-translate-y-0.5 hover:shadow-md
                        active:scale-[.98] active:translate-y-0 active:shadow-sm">
                        Masuk
                    </button>
                </form>

                <p class="mt-10 text-center text-[13.5px] text-cream-600">
                    Mengalami kendala saat masuk?
                    <a href="#" class="font-semibold text-sage-700 hover:text-sage-800 transition-colors">
                        Hubungi pengelola
                    </a>
                </p>

            </div>
        </section>
    </div>

    @vite(['resources/js/app.js'])
</body>

</html>
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
    }
</script>
