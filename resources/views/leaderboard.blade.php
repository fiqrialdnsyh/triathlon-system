<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Leaderboard - {{ $event->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

        :root {
            --ink:        #0B1B33;
            --ink-deep:   #071022;
            --gold:       #C99A3B;
            --gold-bright:#E4B75B;
            --ember:      #D9502E;
            --ember-bright:#EB7952;
            --silver:     #C7CDD6;
            --bronze:     #B87A45;
            --line:       #1B2B45;
        }

        body { font-family: 'Inter', sans-serif; background: var(--ink-deep); color: #E7E9EE; }
        .font-display { font-family: 'Oswald', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .live-pulse { position: relative; width: 8px; height: 8px; border-radius: 50%; background: #57C776; }
        .live-pulse::before {
            content: ''; position: absolute; inset: 0; border-radius: 50%;
            border: 1.5px solid #57C776; animation: radar 1.6s ease-out infinite;
        }
        @keyframes radar { 0% { transform: scale(1); opacity: 0.9; } 100% { transform: scale(4); opacity: 0; } }

        .rank-chip {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 34px; height: 34px; border-radius: 4px;
            font-family: 'Oswald', sans-serif; font-weight: 700; font-size: 15px;
        }
        .rank-gold   { background: color-mix(in srgb, var(--gold) 18%, transparent); color: var(--gold-bright); border: 1px solid color-mix(in srgb, var(--gold) 45%, transparent); }
        .rank-silver { background: color-mix(in srgb, var(--silver) 14%, transparent); color: var(--silver); border: 1px solid color-mix(in srgb, var(--silver) 35%, transparent); }
        .rank-bronze { background: color-mix(in srgb, var(--bronze) 18%, transparent); color: #D79E6B; border: 1px solid color-mix(in srgb, var(--bronze) 40%, transparent); }
        .rank-plain  { color: #5B6472; font-family: 'JetBrains Mono', monospace; font-weight: 500; font-size: 13px; }
    </style>
</head>

<!-- Tambahkan state showResetModal di tag body -->
<body class="antialiased flex flex-col min-h-screen" x-data="{ showResetModal: false }">

    <nav class="w-full px-8 py-5 bg-[var(--ink)] flex items-center justify-between z-50 border-b border-[var(--line)]">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 flex items-center justify-center bg-[var(--gold)] text-[var(--ink-deep)] font-display font-bold text-sm rounded-sm">FT</span>
            <span class="font-display font-bold text-lg tracking-wide uppercase text-white">FTI Lampung</span>
        </div>
        <a href="{{ url('/') }}"
            class="bg-white/5 text-white px-5 py-2.5 rounded-sm border border-white/10 hover:bg-white/10 transition font-display font-medium tracking-widest text-[10px] uppercase">
            &larr; Kembali
        </a>
    </nav>

    <header class="w-full bg-[var(--ink)] border-b border-[var(--line)] py-10 px-8">
        <div class="max-w-screen-2xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="border-l-[3px] border-[var(--gold)] pl-4">
                <p class="text-[var(--gold)] font-mono font-semibold text-[10px] tracking-[0.2em] uppercase mb-2">Live Ranking &amp; Leaderboard</p>
                <h1 class="text-3xl lg:text-5xl font-display font-bold uppercase tracking-tight text-white">
                    {{ $event->name }}
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <!-- Tombol Reset Khusus Admin -->
                @auth
                    <form id="resetForm" action="{{ route('events.reset', $event->id) }}" method="POST" class="m-0 p-0">
                        @csrf
                        <!-- Ubah button type ke button biasa dan trigger showResetModal -->
                        <button type="button" @click="showResetModal = true" class="bg-[var(--ember)] text-white px-5 py-2.5 rounded-sm hover:bg-[#c2451f] transition font-display font-medium tracking-widest text-[10px] uppercase flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset Waktu
                        </button>
                    </form>
                @endauth

                <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-sm px-4 py-2.5">
                    <span class="live-pulse"></span>
                    <span class="text-[#57C776] font-mono font-semibold tracking-widest text-[11px] uppercase">Live Data Active</span>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 py-10 flex-1" x-data="{
        athletes: {{ $event->athletes->toJson() }},
        categories: {{ json_encode(is_array($event->categories) ? array_keys($event->categories) : ['Umum']) }},
        activeCategory: '{{ is_array($event->categories) ? array_keys($event->categories)[0] : 'Umum' }}',
        now: Date.now(),

        init() {
            setInterval(() => {
                this.now = Date.now();
            }, 1000);

            setInterval(() => {
                fetch(`/live-data/event/{{ $event->id }}/athletes`)
                    .then(response => response.json())
                    .then(data => {
                        this.athletes = data;
                    })
                    .catch(error => console.error('Gagal mengambil data live:', error));
            }, 2000);
        },

        getLiveTime(athlete) {
            if (!athlete.start_time) return '00:00:00';
            if (athlete.is_finished) return athlete.formatted_time;

            let startMs = new Date(athlete.start_time).getTime();
            let diffSecs = Math.floor((this.now - startMs) / 1000);

            if (diffSecs < 0) diffSecs = 0;

            let h = String(Math.floor(diffSecs / 3600)).padStart(2, '0');
            let m = String(Math.floor((diffSecs % 3600) / 60)).padStart(2, '0');
            let s = String(diffSecs % 60).padStart(2, '0');

            return `${h}:${m}:${s}`;
        },

        getSortedAthletes(gender) {
            return this.athletes
                .filter(a => a.category === this.activeCategory && a.gender === gender)
                .sort((a, b) => {
                    if (a.is_finished && !b.is_finished) return -1;
                    if (!a.is_finished && b.is_finished) return 1;

                    if (b.laps !== a.laps) {
                        return b.laps - a.laps;
                    }

                    let timeA = a.time_seconds > 0 ? a.time_seconds : 999999;
                    let timeB = b.time_seconds > 0 ? b.time_seconds : 999999;
                    return timeA - timeB;
                });
        }
    }">

        <!-- Flash Message jika sukses Reset -->
        @if (session('success'))
            <div class="mb-6 bg-[#0F2A1B] border border-[#1F4A2E] text-[#57C776] px-4 py-3 rounded-sm text-sm font-mono tracking-wide">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-[var(--ink)] p-1.5 rounded-sm flex overflow-x-auto gap-1 mb-8 border border-[var(--line)]">
            <template x-for="cat in categories" :key="cat">
                <button @click="activeCategory = cat"
                    :class="activeCategory === cat ? 'bg-[var(--gold)] text-[var(--ink-deep)]' : 'bg-transparent text-white/40 hover:bg-white/5 hover:text-white/70'"
                    class="px-6 py-3 rounded-sm text-xs font-display font-semibold uppercase tracking-widest transition whitespace-nowrap"
                    x-text="cat">
                </button>
            </template>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- KOLOM KIRI: PRIA -->
            <div class="flex flex-col gap-4">
                <div class="bg-[var(--ink)] rounded-t-sm px-6 py-4 border-t-2 border-[var(--gold)]">
                    <h2 class="text-white font-display font-semibold text-base tracking-widest uppercase flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--gold-bright)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Kategori Pria (Men)
                    </h2>
                </div>

                <div class="bg-[var(--ink)] rounded-b-sm border border-t-0 border-[var(--line)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="text-[10px] text-white/40 font-mono uppercase tracking-widest bg-[var(--ink-deep)] border-b border-[var(--line)]">
                                <tr>
                                    <th scope="col" class="px-5 py-4">Pos</th>
                                    <th scope="col" class="px-5 py-4">Nama Atlet</th>
                                    <th scope="col" class="px-5 py-4">BIB</th>
                                    <th scope="col" class="px-5 py-4 text-center">Lap</th>
                                    <th scope="col" class="px-5 py-4 text-right">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--line)]">
                                <template x-for="(athlete, index) in getSortedAthletes('male')" :key="athlete.id">
                                    <tr class="hover:bg-white/[0.03] transition">
                                        <td class="px-5 py-4">
                                            <span class="rank-chip" :class="index === 0 ? 'rank-gold' : index === 1 ? 'rank-silver' : index === 2 ? 'rank-bronze' : 'rank-plain'" x-text="index < 3 ? (index + 1) : '#' + (index + 1)"></span>
                                        </td>
                                        <td class="px-5 py-4 font-medium text-white text-sm">
                                            <span x-text="athlete.name"></span>
                                            <span class="block text-[9px] text-white/40 font-mono font-normal uppercase mt-0.5" x-text="athlete.club_name || 'Independen'"></span>
                                        </td>
                                        <td class="px-5 py-4 font-mono text-white/40 text-xs" x-text="athlete.bib_number"></td>
                                        <td class="px-5 py-4 text-center font-display font-semibold text-[var(--gold-bright)] text-sm">
                                            <span x-show="!athlete.is_finished" x-text="athlete.laps"></span>
                                            <span x-show="athlete.is_finished" class="text-[#57C776] tracking-widest uppercase text-[10px]">Finish</span>
                                        </td>
                                        <td class="px-5 py-4 text-right font-mono font-semibold text-[#57C776] text-sm" x-text="getLiveTime(athlete)"></td>
                                    </tr>
                                </template>

                                <tr x-show="athletes.filter(a => a.category === activeCategory && a.gender === 'male').length === 0">
                                    <td colspan="5" class="px-5 py-10 text-center text-white/30 font-mono uppercase tracking-widest text-xs">Belum ada atlet pria di kategori ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: WANITA -->
            <div class="flex flex-col gap-4">
                <div class="bg-[var(--ink)] rounded-t-sm px-6 py-4 border-t-2 border-[var(--ember)]">
                    <h2 class="text-white font-display font-semibold text-base tracking-widest uppercase flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--ember-bright)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Kategori Wanita (Women)
                    </h2>
                </div>

                <div class="bg-[var(--ink)] rounded-b-sm border border-t-0 border-[var(--line)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="text-[10px] text-white/40 font-mono uppercase tracking-widest bg-[var(--ink-deep)] border-b border-[var(--line)]">
                                <tr>
                                    <th scope="col" class="px-5 py-4">Pos</th>
                                    <th scope="col" class="px-5 py-4">Nama Atlet</th>
                                    <th scope="col" class="px-5 py-4">BIB</th>
                                    <th scope="col" class="px-5 py-4 text-center">Lap</th>
                                    <th scope="col" class="px-5 py-4 text-right">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--line)]">
                                <template x-for="(athlete, index) in getSortedAthletes('female')" :key="athlete.id">
                                    <tr class="hover:bg-white/[0.03] transition">
                                        <td class="px-5 py-4">
                                            <span class="rank-chip" :class="index === 0 ? 'rank-gold' : index === 1 ? 'rank-silver' : index === 2 ? 'rank-bronze' : 'rank-plain'" x-text="index < 3 ? (index + 1) : '#' + (index + 1)"></span>
                                        </td>
                                        <td class="px-5 py-4 font-medium text-white text-sm">
                                            <span x-text="athlete.name"></span>
                                            <span class="block text-[9px] text-white/40 font-mono font-normal uppercase mt-0.5" x-text="athlete.club_name || 'Independen'"></span>
                                        </td>
                                        <td class="px-5 py-4 font-mono text-white/40 text-xs" x-text="athlete.bib_number"></td>
                                        <td class="px-5 py-4 text-center font-display font-semibold text-[var(--ember-bright)] text-sm">
                                            <span x-show="!athlete.is_finished" x-text="athlete.laps"></span>
                                            <span x-show="athlete.is_finished" class="text-[#57C776] tracking-widest uppercase text-[10px]">Finish</span>
                                        </td>
                                        <td class="px-5 py-4 text-right font-mono font-semibold text-[#57C776] text-sm" x-text="getLiveTime(athlete)"></td>
                                    </tr>
                                </template>

                                <tr x-show="athletes.filter(a => a.category === activeCategory && a.gender === 'female').length === 0">
                                    <td colspan="5" class="px-5 py-10 text-center text-white/30 font-mono uppercase tracking-widest text-xs">Belum ada atlet wanita di kategori ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Konfirmasi Reset -->
    <div x-show="showResetModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center">
        <!-- Latar belakang gelap (Backdrop) -->
        <div class="absolute inset-0 bg-[var(--ink-deep)]/80 backdrop-blur-sm" x-transition.opacity @click="showResetModal = false"></div>

        <!-- Kotak Modal -->
        <div class="relative bg-[var(--ink)] border border-[var(--line)] rounded-sm shadow-2xl w-full max-w-md p-8 overflow-hidden" x-transition>

            <div class="flex items-center gap-4 mb-6 border-b border-[var(--line)] pb-4">
                <div class="w-10 h-10 rounded-full bg-[var(--ember)]/10 flex items-center justify-center text-[var(--ember-bright)] shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-white font-display font-semibold text-xl tracking-wide uppercase">Reset Waktu Lomba?</h3>
            </div>

            <p class="text-sm text-white/60 mb-8 leading-relaxed">
                Tindakan ini akan <strong class="text-white">menghapus seluruh catatan waktu dan putaran (lap)</strong> semua atlet secara permanen. Data pendaftar dan pasangan (pairing) RFID akan tetap aman. Apakah Anda yakin ingin mengulang balapan dari nol?
            </p>

            <div class="flex gap-3">
                <button type="button" @click="showResetModal = false" class="w-1/2 bg-transparent text-white/60 border border-[var(--line)] font-display font-medium px-4 py-3 rounded-sm text-xs uppercase tracking-widest hover:bg-white/5 hover:text-white transition">Batal</button>
                <button type="button" @click="document.getElementById('resetForm').submit()" class="w-1/2 bg-[var(--ember)] text-white font-display font-semibold px-4 py-3 rounded-sm text-xs uppercase tracking-widest hover:bg-[#c2451f] transition">Ya, Reset Waktu</button>
            </div>
        </div>
    </div>

</body>
</html>
