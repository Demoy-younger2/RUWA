/*
 * ============================================================
 * SiRW Digital - Firmware IoT ESP32 RFID / NFC Reader (e-KTP & Mifare)
 * Proyek: Sistem Informasi Manajemen Rukun Warga (RW 05 Sukamaju)
 *
 * Wiring RC522 ke Arduino Nano ESP32:
 *   RC522   | Pin Arduino Nano ESP32
 *   --------|-----------------------
 *   SDA(SS) | Pin D10 (atau GPIO 21)
 *   SCK     | Pin D13 (atau GPIO 48)
 *   MOSI    | Pin D11 (atau GPIO 38)
 *   MISO    | Pin D12 (atau GPIO 47)
 *   RST     | Pin D9  (atau GPIO 18)
 *   GND     | GND
 *   3.3V    | 3V3    ⚠️ (PERINGATAN: Jangan ke 5V / VBUS!)
 *
 * Wiring RC522 ke ESP32 Standar (DevKit 30/38 pin):
 *   RC522   | Pin ESP32 DevKit
 *   --------|-----------------
 *   SDA(SS) | GPIO 5
 *   SCK     | GPIO 18
 *   MOSI    | GPIO 23
 *   MISO    | GPIO 19
 *   RST     | GPIO 22
 *   GND     | GND
 *   3.3V    | 3.3V
 * ============================================================
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiClientSecure.h>
#include <SPI.h>
#include <MFRC522.h>
// Note: Tanpa dependency ArduinoJson agar langsung bisa dicompile tanpa instalasi tambahan!

// ================= PILIH BOARD YANG DIGUNAKAN =================
// Aktifkan SALAH SATU baris define di bawah ini sesuai modul ESP32 Anda:
//#define USE_ARDUINO_NANO_ESP32   // <-- Aktif jika menggunakan Arduino Nano ESP32
//#define USE_ESP32_S3           // <-- Aktif jika menggunakan ESP32-S3 Dev Module
#define USE_ESP32_DEVKIT         // <-- Aktif jika menggunakan ESP32 DevKit Standar (WROOM 30/38 pin)

#if defined(USE_ARDUINO_NANO_ESP32)
  // Pinout Arduino Nano ESP32
  #if defined(D10) && defined(D9)
    #define SS_PIN    D10  // GPIO 21
    #define RST_PIN   D9   // GPIO 18
  #else
    #define SS_PIN    21   // D10 / GPIO 21
    #define RST_PIN   18   // D9  / GPIO 18
  #endif
  #ifndef LED_BUILTIN
    #define LED_INDICATOR 13
  #else
    #define LED_INDICATOR LED_BUILTIN
  #endif
  #define BUZZER_PIN    -1  // Opsional: pin buzzer jika ada (misal D2 / GPIO 5)

#elif defined(USE_ESP32_S3)
  // Pinout ESP32-S3 Dev Module
  #define SS_PIN    10  // SDA (SS)
  #define RST_PIN   9   // RST
  #define LED_INDICATOR -1
  #define BUZZER_PIN    -1

#else
  // Pinout ESP32 DevKit Standar (30/38 Pin WROOM)
  #define SS_PIN    5   // SDA / SS Pin
  #define RST_PIN   22  // Reset Pin
  #define LED_INDICATOR 2   // Built-in LED atau GPIO 2
  #define BUZZER_PIN    15  // Pin Buzzer
#endif

// ================= KONFIGURASI WIFI & SERVER API =================
const char* WIFI_SSID     = "demoy";       // Ganti dengan SSID WiFi Anda
const char* WIFI_PASSWORD = "MIMPIibadah";   // Ganti dengan Password WiFi Anda

// Alamat API Server Live VPS SiRW Digital (HTTPS):
const char* API_URL       = "https://ruwa.unique-monsters.my.id/api/iot/rfid/scan";
const char* DEVICE_ID     = "ESP32-SCANNER-01";

// Inisialisasi Objek Sensor MFRC522
MFRC522 mfrc522(SS_PIN, RST_PIN);

// Variabel Debounce agar tidak spam tap berulang kali
unsigned long lastScanTime = 0;
String lastScannedUid = "";
const unsigned long SCAN_COOLDOWN = 2000; // 2 detik jeda

void triggerFeedback(bool isSuccess, int beeps = 1) {
  if (LED_INDICATOR >= 0) {
    digitalWrite(LED_INDICATOR, HIGH);
  }

  if (BUZZER_PIN >= 0) {
    for (int i = 0; i < beeps; i++) {
      digitalWrite(BUZZER_PIN, HIGH);
      delay(isSuccess ? 120 : 350);
      digitalWrite(BUZZER_PIN, LOW);
      if (beeps > 1) delay(80);
    }
  } else {
    delay(isSuccess ? 200 : 600);
  }

  if (LED_INDICATOR >= 0) {
    digitalWrite(LED_INDICATOR, LOW);
  }
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  if (LED_INDICATOR >= 0) {
    pinMode(LED_INDICATOR, OUTPUT);
    digitalWrite(LED_INDICATOR, LOW);
  }

  if (BUZZER_PIN >= 0) {
    pinMode(BUZZER_PIN, OUTPUT);
    digitalWrite(BUZZER_PIN, LOW);
  }

  Serial.println("\n============================================================");
  Serial.println("   SiRW Digital - ESP32 IoT e-KTP / RFID Scanner Reader     ");
  Serial.println("============================================================");

  // Inisialisasi Bus SPI & RC522
  SPI.begin();
  mfrc522.PCD_Init();
  delay(100);

  // Verifikasi Komunikasi dengan Reader RC522
  byte version = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  Serial.print("Status Reader RC522 Register Version: 0x");
  Serial.println(version, HEX);
  if (version == 0x00 || version == 0xFF) {
    Serial.println("⚠️ [PERINGATAN] RC522 tidak terdeteksi! Periksa kabel SPI dan tegangan 3.3V.");
  } else {
    Serial.println("✅ [OK] Sensor RFID-RC522 Berhasil Terdeteksi!");
  }

  // ================= KONEKSI WIFI DENGAN DIAGNOSA OTOMATIS =================
  Serial.println("\n--- Memindai Jaringan WiFi 2.4 GHz di Sekitar ---");
  WiFi.mode(WIFI_STA);
  WiFi.disconnect(true);
  delay(100);

  int n = WiFi.scanNetworks();
  bool ssidDitemukan = false;
  if (n == 0) {
    Serial.println("⚠️ Tidak ada jaringan WiFi yang terdeteksi di sekitar!");
  } else {
    Serial.print("Ditemukan ");
    Serial.print(n);
    Serial.println(" jaringan WiFi:");
    for (int i = 0; i < n; ++i) {
      Serial.print("   ");
      Serial.print(i + 1);
      Serial.print(". \"");
      Serial.print(WiFi.SSID(i));
      Serial.print("\" (Sinyal: ");
      Serial.print(WiFi.RSSI(i));
      Serial.println(" dBm)");
      if (WiFi.SSID(i) == WIFI_SSID) {
        ssidDitemukan = true;
      }
    }
  }

  if (!ssidDitemukan) {
    Serial.println("\n⚠️ [PERINGATAN] Nama WiFi \"" + String(WIFI_SSID) + "\" TIDAK DITEMUKAN di daftar scan!");
    Serial.println("   👉 ESP32 HANYA MENDUKUNG FREKUENSI 2.4 GHz (TIDAK BISA 5 GHz).");
    Serial.println("   👉 Jika memakai Hotspot HP: Masuk ke Pengaturan Hotspot -> ganti AP Band ke \"2.4 GHz\".");
  }

  Serial.print("\nMenghubungkan ke WiFi: \"");
  Serial.print(WIFI_SSID);
  Serial.println("\"...");

  WiFi.setSleep(false); // Maksimalkan daya radio WiFi
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int wifiTimeout = 0;
  while (WiFi.status() != WL_CONNECTED && wifiTimeout < 40) {
    delay(500);
    Serial.print(".");
    wifiTimeout++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✅ [OK] WiFi BERHASIL TERHUBUNG!");
    Serial.print("   IP Address ESP32: ");
    Serial.println(WiFi.localIP());
    Serial.print("   Kekuatan Sinyal : ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm");
    triggerFeedback(true, 2);
  } else {
    Serial.println("\n❌ [GAGAL TERHUBUNG]");
    Serial.println("   Status Error WiFi Code: " + String(WiFi.status()));
    Serial.println("   Periksa apakah password WiFi sudah benar dan huruf besar/kecil tepat.");
  }

  Serial.println("\n>> Scanner Siap. Silakan tempelkan kartu e-KTP / RFID Mifare...");
}

void loop() {
  // Cek apakah ada kartu baru terdeteksi
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }

  // Baca serial kartu
  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }

  // Konversi Byte UID ke format Heksadesimal Uppercase
  String cardUid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) cardUid += "0";
    cardUid += String(mfrc522.uid.uidByte[i], HEX);
  }
  cardUid.toUpperCase();

  // Hindari spam baca kartu yang sama dalam jeda 2 detik
  unsigned long now = millis();
  if (cardUid == lastScannedUid && (now - lastScanTime < SCAN_COOLDOWN)) {
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();
    return;
  }

  lastScannedUid = cardUid;
  lastScanTime = now;

  Serial.println("\n------------------------------------------------------------");
  Serial.print("💳 [KARTU TERDETEKSI] UID: ");
  Serial.println(cardUid);

  // Kirim data scan ke API Server SiRW
  sendUidToServer(cardUid);

  // Hentikan enkripsi & pembacaan kartu saat ini
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
}

void sendUidToServer(String uid) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("❌ [ERROR] WiFi tidak tersambung. Reconnecting...");
    WiFi.reconnect();
    triggerFeedback(false, 2);
    return;
  }

  WiFiClientSecure client;
  client.setInsecure(); // Izinkan koneksi HTTPS ke VPS tanpa repot pasang sertifikat root CA

  HTTPClient http;
  if (!http.begin(client, API_URL)) {
    Serial.println("❌ [ERROR] Gagal menginisialisasi koneksi ke server VPS!");
    triggerFeedback(false, 1);
    return;
  }

  http.addHeader("Content-Type", "application/json");

  // Siapkan Payload JSON secara native tanpa perlu library eksternal
  String requestBody = "{\"uid\":\"" + uid + "\",\"device_id\":\"" + String(DEVICE_ID) + "\"}";

  Serial.println(">> Mengirim POST Tap Kartu ke Server SiRW:");
  Serial.print("   URL: ");
  Serial.println(API_URL);
  Serial.print("   Payload: ");
  Serial.println(requestBody);

  int httpCode = http.POST(requestBody);

  if (httpCode > 0) {
    String response = http.getString();
    Serial.print("✅ [HTTP SUCCESS] Status Code: ");
    Serial.println(httpCode);
    Serial.print("   Respon Backend: ");
    Serial.println(response);

    // SKENARIO 1: REGISTRASI SUKSES KE USER LOGIN
    if (response.indexOf("REGISTRATION_SUCCESS") >= 0) {
      Serial.println("🎉 [SUKSES REGISTRASI] Kartu e-KTP berhasil didaftarkan untuk user login!");
      triggerFeedback(true, 3); // 3 bip merdu tanda sukses terdaftar
    }
    // SKENARIO 2: KARTU SUDAH TERDAFTAR DI AKUN LAIN (DITOLAK)
    else if (response.indexOf("DUPLICATE_CARD") >= 0) {
      Serial.println("⛔ [DITOLAK] KARTU SUDAH DIGUNAKAN DI AKUN LAIN! Tidak dapat didaftarkan.");
      triggerFeedback(false, 2); // 2 bip panjang tanda penolakan duplikat
    }
    // SKENARIO 3: AKSES / IDENTIFIKASI TAP DITERIMA
    else if (response.indexOf("ACCESS_GRANTED") >= 0) {
      Serial.println("✅ [AKSES DITERIMA] Identitas kartu diverifikasi!");
      triggerFeedback(true, 1); // 1 bip konfirmasi akses diterima
    }
    // SKENARIO 4: KARTU BELUM TERDAFTAR
    else if (response.indexOf("UNREGISTERED_CARD") >= 0) {
      Serial.println("⚠️ [KARTU BELUM TERDAFTAR] Buka web SiRW lalu klik 'Scan e-KTP' untuk mendaftar.");
      triggerFeedback(false, 1);
    }
    else {
      Serial.println("ℹ️ [INFO] Data tap telah diproses oleh server.");
    }
  } else {
    Serial.print("❌ [HTTP ERROR] Gagal menghubungi server SiRW. Error Code: ");
    Serial.print(httpCode);
    Serial.print(" - ");
    Serial.println(http.errorToString(httpCode));
    triggerFeedback(false, 1);
  }

  http.end();
}
