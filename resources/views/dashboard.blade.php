<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - FTI Lampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap');

        :root {
            --ink: #0B1B33;
            --ink-deep: #071022;
            --cream: #F6F2E8;
            --paper: #FFFDF8;
            --gold: #C99A3B;
            --gold-bright: #E4B75B;
            --ember: #D9502E;
            --slate: #5B6472;
            --line: #E4DCC9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--ink);
        }

        .font-display {
            font-family: 'Oswald', sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .bib-card {
            position: relative;
            background: var(--ink);
            border-radius: 4px;
        }

        .bib-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 24px;
            right: 24px;
            height: 1px;
            background-image: radial-gradient(circle, var(--cream) 1.6px, transparent 1.7px);
            background-size: 14px 1px;
            background-repeat: repeat-x;
            transform: translateY(-1px);
        }

        .bib-pin {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--cream);
            position: absolute;
            top: -3.5px;
            box-shadow: 0 0 0 2px var(--ink);
        }

        .bib-number {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .scan-pulse {
            position: relative;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--gold-bright);
        }

        .scan-pulse::before,
        .scan-pulse::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 1.5px solid var(--gold-bright);
            animation: radar 1.4s ease-out infinite;
            opacity: 0;
        }

        .scan-pulse::after {
            animation-delay: 0.5s;
        }

        @keyframes radar {
            0% {
                transform: scale(1);
                opacity: 0.9;
            }

            100% {
                transform: scale(4.5);
                opacity: 0;
            }
        }

        .rfid-armed {
            color: var(--gold-bright) !important;
        }

        ::selection {
            background: var(--gold);
            color: var(--ink);
        }
    </style>
</head>

<body class="antialiased flex flex-col min-h-screen" x-data="{ showEventModal: false }">

    <nav class="w-full px-8 py-5 bg-[var(--paper)] flex items-center justify-between z-50 border-b border-[var(--line)]">
        <div class="flex items-center gap-3">
            <span
                class="w-8 h-8 flex items-center justify-center bg-[var(--ink)] text-[var(--gold-bright)] font-display font-bold text-sm rounded-sm">FT</span>
            <span class="font-display font-bold text-lg tracking-wide uppercase text-[var(--ink)]">FTI Lampung</span>
        </div>
        <div
            class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest text-[var(--slate)] uppercase">
            <a href="{{ url('/') }}" class="hover:text-[var(--ink)] transition">Beranda</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                    class="bg-[var(--ink)] text-[var(--gold-bright)] px-6 py-2.5 rounded-sm hover:bg-[var(--ink-deep)] transition font-display font-semibold tracking-widest text-[10px] uppercase">
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <main class="w-full max-w-6xl mx-auto px-8 py-12 flex flex-col gap-8">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 border-b border-[var(--line)] pb-6">
            <div class="border-l-[3px] border-[var(--gold)] pl-4">
                <p class="text-[var(--gold)] font-mono font-semibold text-[10px] tracking-[0.2em] uppercase mb-1">Panel
                    Kendali &middot; Admin</p>
                <h2
                    class="text-3xl md:text-4xl font-display font-bold uppercase leading-none tracking-tight text-[var(--ink)]">
                    Manajemen Event
                </h2>
            </div>
            <button @click="showEventModal = true"
                class="bg-[var(--ember)] text-white font-display font-semibold px-6 py-3 rounded-sm text-xs uppercase tracking-widest hover:bg-[#c2451f] transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Event Baru
            </button>
        </div>

        @if (session('success'))
            <div
                class="bg-[#EFF6EC] border-l-[3px] border-[#4C8A5B] text-[#2E5B3A] px-4 py-3 rounded-sm text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-6">
            @forelse($events as $event)
                <div x-data="{ expanded: false, activeTab: 'register' }" class="bib-card shadow-sm overflow-hidden">
                    <span class="bib-pin" style="left: 20px;"></span>
                    <span class="bib-pin" style="right: 20px;"></span>

                    <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-6 cursor-pointer hover:bg-white/[0.03] transition"
                        @click="expanded = !expanded">
                        <div class="flex items-center gap-6 w-full">
                            <div
                                class="bg-[var(--ink-deep)] text-center rounded-sm p-3 min-w-[70px] border border-white/10">
                                <span
                                    class="block text-[var(--gold-bright)] font-mono font-semibold text-[10px] tracking-widest uppercase">{{ \Carbon\Carbon::parse($event->event_date)->format('M') }}</span>
                                <span
                                    class="block text-white bib-number text-xl">{{ \Carbon\Carbon::parse($event->event_date)->format('Y') }}</span>
                            </div>
                            <div>
                                <span
                                    class="bg-[var(--gold)] text-[var(--ink-deep)] text-[9px] font-display font-semibold px-2 py-0.5 rounded-sm uppercase tracking-widest mb-2 inline-block">Berjalan</span>
                                <h3
                                    class="text-white font-display font-semibold text-xl md:text-2xl tracking-wide uppercase">
                                    {{ $event->name }}</h3>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span
                                        class="text-white/50 font-mono text-[10px] uppercase tracking-wide">{{ $event->athletes->count() }}
                                        pendaftar</span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-3">
                            <span
                                class="text-[var(--gold-bright)] font-display font-medium text-xs tracking-widest uppercase"
                                x-text="expanded ? 'Tutup Kelola' : 'Kelola Data'"></span>

                            <div
                                class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-[var(--gold-bright)]">
                                <svg class="w-4 h-4 transform transition-transform duration-300"
                                    :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" @click.stop onsubmit="return confirm('Yakin ingin menghapus event ini? Semua data atlet dan waktu tempuh akan ikut terhapus secara permanen.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-[var(--ember)]/10 text-[var(--ember)] flex items-center justify-center hover:bg-[var(--ember)] hover:text-white transition" title="Hapus Event">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div x-show="expanded" style="display: none;" class="bg-[var(--ink-deep)] border-t border-white/10">
                        <div class="flex border-b border-white/10 px-6 md:px-8 pt-4">
                            <button @click="activeTab = 'register'"
                                :class="activeTab === 'register' ? 'border-[var(--gold-bright)] text-[var(--gold-bright)]' :
                                    'border-transparent text-white/40 hover:text-white/70'"
                                class="pb-3 px-4 font-display font-medium text-xs uppercase tracking-widest border-b-2 transition">
                                1 &middot; Input Pendaftar
                            </button>
                            <button @click="activeTab = 'pairing'"
                                :class="activeTab === 'pairing' ? 'border-[var(--gold-bright)] text-[var(--gold-bright)]' :
                                    'border-transparent text-white/40 hover:text-white/70'"
                                class="pb-3 px-4 font-display font-medium text-xs uppercase tracking-widest border-b-2 transition">
                                2 &middot; Pairing RFID Tag
                            </button>
                        </div>

                        <div class="p-6 md:p-8">
                            <div x-show="activeTab === 'register'">
                                <form action="{{ route('athletes.store') }}" method="POST" class="space-y-6 max-w-3xl">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-medium text-white/40 uppercase tracking-widest mb-2">Nomor
                                                BIB</label>
                                            <input type="text" name="bib_number" required
                                                class="w-full bg-transparent font-mono text-white border-b border-white/20 py-2 text-sm focus:outline-none focus:border-[var(--gold-bright)]"
                                                placeholder="Misal: 101">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-medium text-white/40 uppercase tracking-widest mb-2">Nama
                                                Lengkap</label>
                                            <input type="text" name="name" required
                                                class="w-full bg-transparent text-white border-b border-white/20 py-2 text-sm focus:outline-none focus:border-[var(--gold-bright)]"
                                                placeholder="Nama atlet...">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-medium text-white/40 uppercase tracking-widest mb-2">Asal
                                                Klub / Kota</label>
                                            <input type="text" name="club_name"
                                                class="w-full bg-transparent text-white border-b border-white/20 py-2 text-sm focus:outline-none focus:border-[var(--gold-bright)]"
                                                placeholder="Misal: FTI Lampung / Independen">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-medium text-white/40 uppercase tracking-widest mb-2">Kategori
                                                Pertandingan</label>
                                            <select name="category" required
                                                class="w-full bg-transparent text-white border-b border-white/20 py-2 text-sm focus:outline-none focus:border-[var(--gold-bright)]">
                                                @if (is_array($event->categories) && count($event->categories) > 0)
                                                    @foreach ($event->categories as $catName => $targetLaps)
                                                        <option value="{{ $catName }}"
                                                            class="bg-[var(--ink-deep)]">{{ $catName }} ({{ $targetLaps }} Lap)</option>
                                                    @endforeach
                                                @else
                                                    <option value="Umum" class="bg-[var(--ink-deep)]">Umum</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-medium text-white/40 uppercase tracking-widest mb-2">Gender</label>
                                            <select name="gender" required
                                                class="w-full bg-transparent text-white border-b border-white/20 py-2 text-sm focus:outline-none focus:border-[var(--gold-bright)]">
                                                <option value="male" class="bg-[var(--ink-deep)]">Pria (Men)
                                                </option>
                                                <option value="female" class="bg-[var(--ink-deep)]">Wanita (Women)
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="w-full md:w-auto bg-white/5 text-white border border-white/15 font-display font-medium px-8 py-3 rounded-sm text-xs uppercase tracking-widest hover:bg-white/10 transition">
                                        Simpan Pendaftar
                                    </button>
                                </form>
                            </div>

                            <div x-show="activeTab === 'pairing'" style="display: none;">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm text-white/60">
                                        <thead class="text-[10px] text-white/40 font-mono uppercase tracking-widest">
                                            <tr class="border-b border-white/10">
                                                <th scope="col" class="px-6 py-4">BIB</th>
                                                <th scope="col" class="px-6 py-4">Nama Atlet</th>
                                                <th scope="col" class="px-6 py-4">Klub</th>
                                                <th scope="col" class="px-6 py-4">Kategori</th>
                                                <th scope="col" class="px-6 py-4 text-right">Status RFID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($event->athletes as $athlete)
                                                <tr class="border-b border-white/5 hover:bg-white/[0.03] transition">
                                                    <td class="px-6 py-4 bib-number text-white">
                                                        {{ $athlete->bib_number }}</td>
                                                    <td class="px-6 py-4 text-white font-medium">{{ $athlete->name }}
                                                    </td>
                                                    <td class="px-6 py-4 text-xs italic">
                                                        {{ $athlete->club_name ?: 'Independen' }}</td>
                                                    <td class="px-6 py-4 text-xs">{{ $athlete->category }}
                                                        ({{ ucfirst($athlete->gender) }})</td>
                                                    <td class="px-6 py-4 text-right">
                                                        @if ($athlete->rfid_tag)
                                                            <span
                                                                class="inline-flex items-center gap-2 bg-[#0F2A1B] text-[#6FCB8D] text-xs font-mono px-3 py-1.5 rounded-sm border border-[#1F4A2E]">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                {{ $athlete->rfid_tag }}
                                                            </span>
                                                        @else
                                                            <form action="{{ route('athletes.update_rfid', $athlete->id) }}" method="POST" class="flex items-center justify-end gap-2"
                                                                  x-data="{
                                                                      scanning: false,
                                                                      rfidTag: '',
                                                                      scanInterval: null,
                                                                      scanTimeout: null,

                                                                      startScan() {
                                                                          if (this.scanning) return;

                                                                          this.scanning = true;
                                                                          this.rfidTag = 'Mencari...';

                                                                          this.scanInterval = setInterval(() => {
                                                                              fetch('/live-data/latest-tag')
                                                                                  .then(res => res.json())
                                                                                  .then(data => {
                                                                                      if (data.tag) {
                                                                                          this.rfidTag = data.tag;
                                                                                          this.scanning = false;
                                                                                          clearInterval(this.scanInterval);
                                                                                          clearTimeout(this.scanTimeout);

                                                                                          fetch('/live-data/clear-tag', {
                                                                                              method: 'POST',
                                                                                              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                                                                          });
                                                                                      }
                                                                                  });
                                                                          }, 2000);

                                                                          this.scanTimeout = setTimeout(() => {
                                                                              if (this.scanning) {
                                                                                  this.scanning = false;
                                                                                  clearInterval(this.scanInterval);
                                                                                  this.rfidTag = 'Waktu Habis';

                                                                                  setTimeout(() => {
                                                                                      if (this.rfidTag === 'Waktu Habis') {
                                                                                          this.rfidTag = '';
                                                                                      }
                                                                                  }, 2000);
                                                                              }
                                                                          }, 15000);
                                                                      }
                                                                  }">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="text" name="rfid_tag"
                                                                    x-model="rfidTag" readonly
                                                                    class="w-40 bg-transparent font-mono text-xs border-b border-white/20 px-1 py-1 text-right focus:outline-none"
                                                                    :class="scanning ? 'rfid-armed' : 'text-white/30'"
                                                                    placeholder="Belum di-pairing">

                                                                <button type="button" @click="startScan()"
                                                                    class="w-7 h-7 flex items-center justify-center rounded-full bg-white/5 border border-white/15 hover:border-[var(--gold-bright)] transition"
                                                                    title="Mulai Scan">
                                                                    <span x-show="!scanning"
                                                                        class="w-1.5 h-1.5 rounded-full bg-white/50"></span>
                                                                    <span x-show="scanning" style="display:none"
                                                                        class="scan-pulse"></span>
                                                                </button>

                                                                <button type="submit"
                                                                    class="bg-[var(--gold)] text-[var(--ink-deep)] font-display font-semibold px-3 py-1.5 text-[10px] uppercase tracking-wide rounded-sm hover:bg-[var(--gold-bright)] transition">Simpan</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5"
                                                        class="px-6 py-8 text-center text-white/30 italic text-sm">
                                                        Belum ada atlet terdaftar di event ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="border border-dashed border-[var(--line)] rounded-sm p-10 text-center">
                    <p class="text-[var(--slate)] font-mono tracking-widest uppercase text-xs">Belum ada event yang
                        dibuat.</p>
                </div>
            @endforelse
        </div>
    </main>

    <div x-show="showEventModal" style="display: none;"
        class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="absolute inset-0 bg-[var(--ink-deep)]/80 backdrop-blur-sm" x-transition.opacity
            @click="showEventModal = false"></div>
        <div class="relative bg-[var(--paper)] rounded-sm shadow-2xl w-full max-w-md p-8 overflow-hidden" x-transition>
            <h3
                class="text-[var(--ink)] font-display font-semibold text-xl tracking-wide mb-6 uppercase border-b border-[var(--line)] pb-4">
                Setup Event Baru</h3>
            <form action="{{ route('events.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label
                        class="block text-[10px] font-mono font-medium text-[var(--slate)] uppercase tracking-widest mb-2">Nama
                        Event</label>
                    <input type="text" name="name" required
                        class="w-full bg-transparent border-b border-[var(--line)] py-2 text-sm focus:outline-none focus:border-[var(--gold)]"
                        placeholder="Contoh: Krakatau Seri 1">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-mono font-medium text-[var(--slate)] uppercase tracking-widest mb-2">Tanggal
                        Pelaksanaan</label>
                    <input type="date" name="event_date" required
                        class="w-full bg-transparent border-b border-[var(--line)] py-2 text-sm focus:outline-none focus:border-[var(--gold)]">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-mono font-medium text-[var(--slate)] uppercase tracking-widest mb-2">Kategori & Target Lap (Format: Nama:Lap)</label>
                    <input type="text" name="categories" required
                        class="w-full bg-transparent border-b border-[var(--line)] py-2 text-sm focus:outline-none focus:border-[var(--gold)]"
                        placeholder="Misal: Sprint:3, Olympic:5, Umum:2">
                </div>
                <div class="flex gap-3 pt-4 border-t border-[var(--line)] mt-6">
                    <button type="button" @click="showEventModal = false"
                        class="w-1/3 bg-transparent text-[var(--slate)] border border-[var(--line)] font-display font-medium px-4 py-3 rounded-sm text-xs uppercase tracking-widest hover:bg-black/[0.03] transition">Batal</button>
                    <button type="submit"
                        class="w-2/3 bg-[var(--ink)] text-[var(--gold-bright)] font-display font-semibold px-4 py-3 rounded-sm text-xs uppercase tracking-widest hover:bg-[var(--ink-deep)] transition">Simpan
                        Event</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
