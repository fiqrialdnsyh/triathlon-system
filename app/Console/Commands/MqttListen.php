<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Cache;
use App\Models\Athlete;
use App\Models\RaceLog;

class MqttListen extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Mendengarkan data scan RFID dari MQTT Broker';

    public function handle()
    {
        $server   = 'host.docker.internal';
        $port     = 1883;
        $clientId = 'triathlon_listener_' . uniqid();

        $client = new MqttClient($server, $port, $clientId);
        $connectionSettings = (new ConnectionSettings)->setKeepAliveInterval(60);

        try {
            $client->connect($connectionSettings, true);
            $this->info("Sukses terhubung ke MQTT Broker ($server)");

            $topic = 'itera/triathlon/rfid/scan';
            $this->info("Menunggu scan tag di topik: $topic ...");

            $client->subscribe($topic, function ($topic, $message) {
                $this->processRfidData($message);
            }, 0);

            $client->loop(true);
        } catch (\Exception $e) {
            $this->error("Gagal terhubung: " . $e->getMessage());
        }
    }

    private function processRfidData($message)
    {
        $rawUid = $message;
        $data = json_decode($message, true);
        if (is_array($data) && isset($data['uid'])) {
            $rawUid = $data['uid'];
        }

        $cleanUid = strtoupper(str_replace(' ', '', $rawUid));
        $cacheKey = 'rfid_cooldown_' . $cleanUid;
        $cooldownSeconds = 30;

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, $cooldownSeconds);
        $waktuScan = now()->format('H:i:s');

        $atlet = Athlete::where('rfid_tag', $cleanUid)->first();

        if (!$atlet) {
            $this->warn("[$waktuScan] Tag tidak terdaftar: $cleanUid (Menyimpan ke Cache untuk Pairing...)");
            Cache::put('latest_unpaired_tag', $cleanUid, 60);
            return;
        }

        $riwayatScan = RaceLog::where('athlete_id', $atlet->id)
                              ->orderBy('scanned_at', 'asc')
                              ->get();

        // 1. Ambil target lap untuk kategori atlet tersebut (default 1 jika gagal)
        $kategoriAtlet = $atlet->category;
        $targetLaps = is_array($atlet->event->categories) && isset($atlet->event->categories[$kategoriAtlet])
                        ? $atlet->event->categories[$kategoriAtlet]
                        : 1;

        // 2. Cek apakah atlet sudah pernah Finish sebelumnya?
        $sudahFinish = $riwayatScan->where('reader_location', 'Finish')->isNotEmpty();

        if ($sudahFinish) {
            $this->warn("[$waktuScan] ABAIKAN - Atlet {$atlet->name} sudah FINISH. Waktu tidak dicatat lagi.");
            return; // Hentikan proses, tidak dicatat ke database
        }

        // 3. Logika Penentuan Start vs Lap vs Finish
        if ($riwayatScan->isEmpty()) {
            RaceLog::create([
                'athlete_id'      => $atlet->id,
                'reader_location' => 'Start',
                'scanned_at'      => now()
            ]);
            $this->info("[$waktuScan] MULAI - Atlet {$atlet->name} mulai (Target: $targetLaps Lap).");
        } else {
            // Karena 1 data sudah dipakai untuk "Start", sisa data = jumlah lap yang telah diselesaikan
            $jumlahLapSelesai = $riwayatScan->count();

            if ($jumlahLapSelesai >= $targetLaps) {
                // FINISH
                RaceLog::create([
                    'athlete_id'      => $atlet->id,
                    'reader_location' => 'Finish',
                    'scanned_at'      => now()
                ]);
                $this->info("[$waktuScan] FINISH! - Atlet {$atlet->name} menyelesaikan lomba!");
            } else {
                // LAP BIASA
                RaceLog::create([
                    'athlete_id'      => $atlet->id,
                    'reader_location' => 'Lap ' . $jumlahLapSelesai,
                    'scanned_at'      => now()
                ]);
                $this->info("[$waktuScan] LAP $jumlahLapSelesai - Atlet {$atlet->name} menyelesaikan putaran.");
            }
        }
    }
}
