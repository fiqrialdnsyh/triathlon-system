<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin - FTI Lampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');
        .font-display { font-family: 'Oswald', sans-serif; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased relative min-h-screen flex items-center justify-center overflow-hidden bg-[#0B1B33]">

    <!-- Background Image & Overlay -->
    <div class="absolute inset-0 z-0">
        <!-- Anda bisa mengganti URL gambar ini dengan gambar triathlon lokal di folder public Anda -->
        <img src="https://images.unsplash.com/photo-1555243896-771a81bb1221?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover opacity-20 mix-blend-luminosity" alt="Triathlon Background">
        <div class="absolute inset-0 bg-[#071324]/80"></div>
    </div>

    <!-- Tombol Kembali -->
    <a href="{{ url('/') }}" class="absolute top-8 left-8 z-10 flex items-center gap-2 text-white/70 hover:text-white transition font-display font-medium tracking-widest text-[11px] uppercase">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Beranda
    </a>

    <!-- Kartu Login -->
    <div class="relative z-10 w-full max-w-md bg-[#F4F1E1] rounded-[2rem] p-10 sm:p-12 shadow-2xl mx-4">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-display font-bold uppercase text-[#0B1B33] tracking-wide">Selamat Datang</h2>
            <p class="text-sm text-[#0B1B33]/70 mt-2 font-medium">Silakan masuk ke akun Anda untuk melanjutkan.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-5">
                <label for="email" class="block text-[11px] font-display font-bold text-[#0B1B33]/60 uppercase tracking-widest mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full bg-[#EBF0F6] border-none rounded-xl px-5 py-4 text-sm text-[#0B1B33] placeholder-[#0B1B33]/30 focus:ring-2 focus:ring-[#FFD700] transition" placeholder="admin@triathlon-lampung.id">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-xs" />
            </div>

            <!-- Password -->
            <div class="mb-5">
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-[11px] font-display font-bold text-[#0B1B33]/60 uppercase tracking-widest">Kata Sandi</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] font-medium text-[#0B1B33]/50 hover:text-[#0B1B33] transition">Lupa Sandi?</a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full bg-[#EBF0F6] border-none rounded-xl px-5 py-4 text-sm text-[#0B1B33] placeholder-[#0B1B33]/30 focus:ring-2 focus:ring-[#FFD700] transition" placeholder="••••••••••••">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-xs" />
            </div>

            <!-- Remember Me -->
            <div class="mb-8 flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-[#0B1B33] shadow-sm focus:ring-[#FFD700] w-4 h-4 transition">
                <label for="remember_me" class="ml-2 text-xs font-medium text-[#0B1B33]/70">Ingat Saya</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-[#FFE500] hover:bg-[#F2D900] text-[#0B1B33] font-display font-bold uppercase tracking-widest text-sm py-4 rounded-xl transition shadow-lg shadow-[#FFE500]/20 flex justify-center items-center gap-2">
                Masuk Sekarang
            </button>

        </form>
    </div>

</body>
</html>
