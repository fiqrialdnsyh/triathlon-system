import threading
import queue
import time
import paho.mqtt.client as mqtt
from typing import Iterator

# Mengimpor modul bawaan dari teknisi
from response import hex_readable, Response
from transport import TcpTransport
from reader import Reader

# ==========================================
# KONFIGURASI MQTT BROKER LOKAL
# ==========================================
MQTT_BROKER = "127.0.0.1"
MQTT_PORT = 1883
MQTT_TOPIC = "itera/triathlon/rfid/scan"

# CallbackAPIVersion.VERSION2 ditambahkan agar warning warna kuning di terminal hilang
mqtt_client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)

try:
    mqtt_client.connect(MQTT_BROKER, MQTT_PORT, 60)
    mqtt_client.loop_start()
    print(f"[MQTT] Terhubung ke broker {MQTT_BROKER}:{MQTT_PORT}")
except Exception as e:
    print(f"[MQTT] Gagal terhubung ke broker: {e}")

# ==========================================
# LOGIKA MULTITHREADING (QUEUE PUBLISHER)
# ==========================================
tag_queue = queue.Queue()

def mqtt_publisher():
    while True:
        tag_data = tag_queue.get()
        if tag_data is None:
            break

        try:
            payload = f'{{"uid": "{tag_data}"}}'
            mqtt_client.publish(MQTT_TOPIC, payload)
            print(f"[PUBLISH] Terkirim ke MQTT: {payload}")
        except Exception as e:
            print(f"[PUBLISH ERROR] Gagal mengirim: {e}")

        tag_queue.task_done()

publish_thread = threading.Thread(target=mqtt_publisher, daemon=True)
publish_thread.start()

# ==========================================
# LOGIKA KONEKSI READER (TCP IP)
# ==========================================
if __name__ == "__main__":
    IP_READER = '192.168.1.192'
    PORT_READER = 6000

    # SABUK PENGAMAN 1: Auto-Reconnect jika koneksi terputus total
    while True:
        try:
            print(f"[READER] Mencoba koneksi ke {IP_READER}:{PORT_READER}...")
            transport = TcpTransport(IP_READER, PORT_READER)
            reader = Reader(transport)
            print("[READER] Koneksi berhasil! Memulai pembacaan Active Mode...")

            # SABUK PENGAMAN 2: Mengabaikan bad data dan timeout
            while True:
                try:
                    responses: Iterator[Response] = reader.inventory_active_mode()
                    for response in responses:
                        tag: bytes = response.data
                        if tag:
                            clean_tag = hex_readable(tag).replace(" ", "")
                            tag_queue.put(clean_tag)

                except Exception as inner_e:
                    err_msg = str(inner_e).lower()
                    # Jika error hanya karena sepi tag (timeout) atau data jaringan terpotong (index out of range)
                    if "timed out" in err_msg or "index out of range" in err_msg or "response" in err_msg:
                        continue # Abaikan dan langsung lanjut membaca ulang
                    else:
                        # Jika error berat, lempar ke luar untuk restart koneksi TCP
                        raise inner_e

        except KeyboardInterrupt:
            # Jika kamu menekan CTRL + C di terminal
            print("\n[SYSTEM] Menghentikan program...")
            break
        except Exception as e:
            # Mengistirahatkan sistem sebentar sebelum mencoba menyambung ulang
            print(f"\n[SYSTEM WARNING] Koneksi terputus ({e}). Mencoba ulang dalam 2 detik...")
            time.sleep(2)

    # Cleanup penutupan program
    tag_queue.put(None)
    publish_thread.join()
    mqtt_client.loop_stop()
    mqtt_client.disconnect()
    try:
        if 'reader' in locals():
            reader.close()
    except:
        pass
    print("[SYSTEM] Program selesai ditutup.")
