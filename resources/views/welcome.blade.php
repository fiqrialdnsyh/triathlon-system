<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Triathlon Lampung Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased flex flex-col min-h-screen">

    @php
        // Logika Pemisahan Event
        // 1. Cari event utama (yang paling dekat di masa depan, atau hari ini)
        $featuredEvent = $events->filter(function($e) {
            return \Carbon\Carbon::parse($e->event_date)->startOfDay()->greaterThanOrEqualTo(now()->startOfDay());
        })->sortBy('event_date')->first();

        // Jika tidak ada event di masa depan, ambil event terbaru yang paling terakhir selesai
        if (!$featuredEvent) {
            $featuredEvent = $events->sortByDesc('event_date')->first();
        }

        // 2. Sisa event dimasukkan ke daftar "Lainnya"
        $otherEvents = $featuredEvent ? $events->where('id', '!=', $featuredEvent->id) : collect();
    @endphp

    <!-- Navbar -->
    <nav class="w-full px-8 py-5 bg-white shadow-sm flex items-center justify-between z-50">
        <div class="flex items-center gap-3 text-blue-900 font-black text-xl tracking-tighter uppercase">
            <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9.5 4a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm1 11.5h-2v-6h2v6z"></path></svg>
            </span>
            FTI LAMPUNG
        </div>
        <div class="hidden md:flex items-center gap-8 text-xs font-bold tracking-widest text-gray-500 uppercase">
            <a href="{{ url('/') }}" class="text-gray-900 border-b-2 border-gray-900 pb-1">Beranda</a>

            @auth
                <a href="{{ url('/dashboard') }}" class="bg-gray-900 text-yellow-400 px-6 py-2.5 rounded hover:bg-gray-800 transition shadow-sm">DASHBOARD ADMIN</a>
            @else
                <a href="{{ route('login') }}" class="bg-gray-900 text-yellow-400 px-6 py-2.5 rounded hover:bg-gray-800 transition shadow-sm">LOGIN ADMIN</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative w-full h-[60vh] min-h-[400px] bg-gray-900 flex flex-col items-center justify-center text-center overflow-hidden">
        <img src="https://images.unsplash.com/photo-1557330359-ffb0deed6163?auto=format&fit=crop&w=1920&q=80" alt="Triathlon Background" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay">

        <div class="relative z-10 px-4">
            <h1 class="text-5xl md:text-7xl font-black text-white italic tracking-widest uppercase drop-shadow-lg">
                NEXT-GEN TRIATHLON!
            </h1>
            <a href="#event" class="mt-8 inline-block bg-yellow-400 text-gray-900 font-black px-8 py-3 text-sm uppercase tracking-widest transform -skew-x-12 hover:bg-yellow-300 transition shadow-lg">
                <span class="block transform skew-x-12">Lihat Hasil & Ranking</span>
            </a>
        </div>
    </header>

    <!-- Main Content: Event Utama -->
    <main id="event" class="w-full max-w-7xl mx-auto px-8 py-20 flex flex-col lg:flex-row gap-16">

        <!-- Kolom Kiri -->
        <div class="w-full lg:w-1/2">
            <div class="border-l-4 border-yellow-400 pl-5 mb-6">
                <p class="text-yellow-500 font-extrabold text-[10px] tracking-widest uppercase mb-2">Sistem Manajemen Event</p>
                <h2 class="text-4xl lg:text-5xl font-black uppercase leading-none tracking-tight text-gray-900">
                    MEMBANGUN<br>PRESTASI<br>TRIATHLON LAMPUNG
                </h2>
            </div>

            <p class="text-gray-500 text-sm leading-relaxed mb-8 pr-4 font-medium">
                Platform resmi untuk pencatatan waktu otomatis dan sinkronisasi hardware pembaca RFID. Pilih event di bawah ini untuk melihat papan peringkat (leaderboard) secara real-time, termasuk jumlah lap, waktu tempuh, dan status atlet di lintasan.
            </p>

            <div class="flex flex-wrap gap-3">
                <span class="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-wider border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> RENANG
                </span>
                <span class="flex items-center gap-2 bg-yellow-50 text-yellow-700 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-wider border border-yellow-200">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span> SEPEDA
                </span>
                <span class="flex items-center gap-2 bg-red-50 text-red-700 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-wider border border-red-100">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> LARI
                </span>
            </div>
        </div>

        <!-- Kolom Kanan (Agenda Utama) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center">
            @if($featuredEvent)
                <div class="bg-[#111827] rounded-2xl p-8 shadow-2xl relative overflow-hidden group">
                    <!-- Efek cahaya kuning -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-400 opacity-10 rounded-full blur-3xl group-hover:opacity-20 transition duration-500"></div>

                    <span class="inline-block bg-yellow-400 text-gray-900 text-[9px] font-black px-3 py-1 rounded-sm uppercase tracking-widest mb-6 shadow-sm">
                        {{ $featuredEvent->status === 'active' ? 'ACTIVE RACE' : 'AGENDA UTAMA' }}
                    </span>

                    <div class="flex flex-col sm:flex-row gap-6 items-start relative z-10">
                        <!-- Tanggal Block -->
                        <div class="bg-[#1f2937] text-center rounded-xl p-4 min-w-[80px] border border-gray-800">
                            <span class="block text-yellow-400 font-extrabold text-sm tracking-widest uppercase">{{ \Carbon\Carbon::parse($featuredEvent->event_date)->format('M') }}</span>
                            <span class="block text-white font-black text-2xl mt-1">{{ \Carbon\Carbon::parse($featuredEvent->event_date)->format('Y') }}</span>
                        </div>

                        <!-- Deskripsi Event -->
                        <div class="flex-1">
                            <h3 class="text-white font-black text-2xl tracking-wide uppercase">{{ $featuredEvent->name }}</h3>

                            <div class="flex flex-wrap gap-2 mt-3 mb-4">
                                @if(is_array($featuredEvent->categories) && count($featuredEvent->categories) > 0)
                                    @foreach($featuredEvent->categories as $cat)
                                        <span class="text-gray-400 text-[9px] border border-gray-600 px-2 py-0.5 rounded uppercase font-bold">{{ $cat }}</span>
                                    @endforeach
                                @endif
                            </div>

                            <a href="{{ route('leaderboard', $featuredEvent->id) }}" class="inline-block mt-2 text-gray-900 bg-yellow-400 px-5 py-2.5 rounded text-[10px] font-black hover:bg-yellow-300 uppercase tracking-widest transition shadow-md">
                                Lihat Live Leaderboard &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Area Kosong jika tidak ada event sama sekali -->
                <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center h-full flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-gray-400 font-bold tracking-widest uppercase text-xs">Belum ada event perlombaan.</p>
                </div>
            @endif
        </div>
    </main>

    <!-- Tambahan Layout Bawah: Event Terdahulu / Lainnya -->
    @if($otherEvents->count() > 0)
    <section class="w-full max-w-7xl mx-auto px-8 pb-20">
        <div class="border-t border-gray-200 pt-12 mb-8">
            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Agenda Lainnya & Riwayat Event</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($otherEvents as $event)
                <div class="bg-[#1f2937] border border-gray-800 rounded-2xl p-6 relative overflow-hidden group hover:border-gray-600 transition duration-300">
                    <div class="flex items-center gap-4 relative z-10 mb-4">
                        <div class="bg-[#111827] text-center rounded-lg p-3 min-w-[65px] border border-gray-700">
                            <span class="block text-gray-400 font-extrabold text-xs tracking-widest uppercase">{{ \Carbon\Carbon::parse($event->event_date)->format('M') }}</span>
                            <span class="block text-white font-black text-lg mt-1">{{ \Carbon\Carbon::parse($event->event_date)->format('Y') }}</span>
                        </div>
                        <div>
                            <h4 class="text-white font-black text-lg tracking-wide uppercase leading-tight">{{ $event->name }}</h4>
                            <span class="text-gray-500 text-[10px] font-bold tracking-widest uppercase mt-1 block">
                                {{ \Carbon\Carbon::parse($event->event_date)->isPast() ? 'Selesai' : 'Mendatang' }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('leaderboard', $event->id) }}" class="block text-center w-full text-white bg-[#111827] border border-gray-700 px-4 py-2.5 rounded text-[10px] font-black hover:bg-gray-800 hover:text-yellow-400 uppercase tracking-widest transition">
                        Buka Leaderboard
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-[#0f172a] py-8 border-t border-gray-900 mt-auto">
        <div class="max-w-7xl mx-auto px-8 flex justify-between items-center text-[10px] text-gray-500 font-bold tracking-widest uppercase">
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition">IG</a>
                <a href="#" class="hover:text-white transition">MAIL</a>
            </div>
            <p>&copy; {{ date('Y') }} TRIATHLON LAMPUNG PORTAL</p>
        </div>
    </footer>

</body>
</html>
