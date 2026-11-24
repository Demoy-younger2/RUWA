# 📡 Panduan Integrasi IoT ESP32 RFID / NFC (e-KTP) - SiRW Digital

Dokumentasi implementasi modul pembaca kartu identitas warga (e-KTP / Kartu RFID Mifare 13.56 MHz) menggunakan **Arduino Nano ESP32** dan **ESP32 Standar (DevKit)** yang terintegrasi langsung dengan backend **Laravel 12 REST API**.

---

## 🔌 1. Skema Pengkabelan (Wiring Pinout)

### Opsi A: Wiring RC522 ke Arduino Nano ESP32

| Pin RC522 | Pin Arduino Nano ESP32 | Keterangan |
|---|---|---|
| **SDA (SS)** | **Pin D10** (atau GPIO 21) | SPI Slave Select |
| **SCK** | **Pin D13** (atau GPIO 48) | SPI Clock |
| **MOSI** | **Pin D11** (atau GPIO 38) | SPI Master Out Slave In |
| **MISO** | **Pin D12** (atau GPIO 47) | SPI Master In Slave Out |
| **RST** | **Pin D9** (atau GPIO 18) | Reset Pin |
| **GND** | **GND** | Ground |
| **3.3V** | **3V3** | ⚠️ **(PERINGATAN: Jangan ke 5V / VBUS!)** |

---

### Opsi B: Wiring RC522 ke ESP32 Standar (DevKit 30/38 pin)

| Pin RC522 | Pin ESP32 DevKit | Keterangan |
|---|---|---|
| **SDA (SS)** | **GPIO 5** | SPI Slave Select |
| **SCK** | **GPIO 18** | SPI Clock |
| **MOSI** | **GPIO 23** | SPI Master Out Slave In |
| **MISO** | **GPIO 19** | SPI Master In Slave Out |
| **RST** | **GPIO 22** | Reset Pin |
| **GND** | **GND** | Ground |
| **3.3V** | **3.3V** | Tegangan 3.3V |

---

## 💻 2. Persiapan Arduino IDE

1. Buka software **Arduino IDE**.
2. Masuk ke menu **Tools $\rightarrow$ Manage Libraries...**
3. Install 2 library berikut:
   - **`MFRC522`** by GithubCommunity (Versi terbaru)
   - **`ArduinoJson`** by Benoit Blanchon (Versi 6.x atau 7.x)
4. Pilih Board:
   - Jika memakai **Arduino Nano ESP32**: pilih board **Arduino Nano ESP32** via Board Manager.
   - Jika memakai **ESP32 DevKit**: pilih **ESP32 Dev Module**.

---

## ⚙️ 3. Konfigurasi Firmware

Buka file **[`esp32_rfid_reader.ino`](file:///d:/Rukun_Warga/iot_esp32_firmware/esp32_rfid_reader.ino)** di folder ini:

1. **Pilih Board**:
   ```cpp
   // Aktifkan salah satu sesuai board yang dipakai:
   #define USE_ARDUINO_NANO_ESP32   // <-- Untuk Arduino Nano ESP32
   //#define USE_ESP32_DEVKIT        // <-- Untuk ESP32 DevKit 30/38 Pin
   ```
2. **Koneksi WiFi & Server API**:
   ```cpp
   const char* WIFI_SSID     = "NAMA_WIFI_ANDA";
   const char* WIFI_PASSWORD = "PASSWORD_WIFI_ANDA";

   // Ganti dengan IP lokal komputer server Laravel Anda (cek via cmd: ipconfig)
   const char* API_URL       = "http://192.168.1.50:8000/api/iot/rfid/scan";
   ```
3. Klik tombol **Upload** di Arduino IDE.

---

## 🛡️ 4. Validasi Anti-Duplikasi Kartu

Sistem web SiRW Digital secara otomatis memvalidasi keunikan kartu:
- **Kartu Baru**: Didaftarkan ke user yang sedang membuka sesi scan di web.
- **Kartu Sudah Milik Akun Lain**: Ditolak langsung oleh server dengan kode `DUPLICATE_CARD`. Web menampilkan peringatan merah dan ESP32 memberikan respon bip error.
