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
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h> // Install via Arduino Library Manager: "ArduinoJson" by Benoit Blanchon

// ================= PILIH BOARD YANG DIGUNAKAN =================
// Aktifkan SALAH SATU baris define di bawah ini sesuai board Anda:
#define USE_ARDUINO_NANO_ESP32   // <-- Hapus komentar ini jika menggunakan Arduino Nano ESP32
//#define USE_ESP32_DEVKIT        // <-- Hapus komentar ini jika menggunakan ESP32 Standar (30/38 pin)

#if defined(USE_ARDUINO_NANO_ESP32)
  // Pinout Arduino Nano ESP32
  #if defined(D10) && defined(D9)
    #define SS_PIN    D10  // GPIO 21
    #define RST_PIN   D9   // GPIO 18
  #else
    #define SS_PIN    21   // D10 / GPIO 21
    #define RST_PIN   18   // D9  / GPIO 18
  #endif
  #define LED_INDICATOR LED_BUILTIN
  #define BUZZER_PIN    -1  // Opsional: atur pin buzzer jika ada (misal D2 / GPIO 5)

#else
  // Pinout ESP32 DevKit Standar (30/38 Pin)
  #define SS_PIN    5   // SDA / SS Pin
  #define RST_PIN   22  // Reset Pin
  #define LED_INDICATOR 2   // Built-in LED atau GPIO 2
  #define BUZZER_PIN    15  // Pin Buzzer
#endif

// ================= KONFIGURASI WIFI & SERVER API =================
const char* WIFI_SSID     = "demoy";       // Ganti dengan SSID WiFi Anda
const char* WIFI_PASSWORD = "MIMPIibadah";   // Ganti dengan Password WiFi Anda

// Masukkan Alamat IP Server Laravel SiRW (Cek via cmd di komputer: ipconfig)
// Contoh: "http://192.168.1.100:8000/api/iot/rfid/scan"
const char* API_URL       = "http://192.168.1.50:8000/api/iot/rfid/scan";
const char* DEVICE_ID     = "ESP32-SCANNER-01";

// Inisialisasi Objek Sensor MFRC522
MFRC522 mfrc522(SS_PIN, RST_PIN);

// Variabel Debounce agar tidak spam tap berulang kali
unsigned long lastScanTime = 0;
String lastScannedUid = "";
const unsigned long SCAN_COOLDOWN = 2000; // 2 detik jeda

void triggerFeedback(bool isSuccess, int beeps = 1) {
  #if defined(LED_INDICATOR)
    digitalWrite(LED_INDICATOR, HIGH);
  #endif

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

  #if defined(LED_INDICATOR)
    digitalWrite(LED_INDICATOR, LOW);
  #endif
}

void setup() {
  Serial.begin(115200);
  delay(1000);

  #if defined(LED_INDICATOR)
    pinMode(LED_INDICATOR, OUTPUT);
    digitalWrite(LED_INDICATOR, LOW);
  #endif

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

  // Koneksi WiFi
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.println(WIFI_SSID);
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int wifiTimeout = 0;
  while (WiFi.status() != WL_CONNECTED && wifiTimeout < 30) {
    delay(500);
    Serial.print(".");
    wifiTimeout++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✅ [OK] WiFi Terhubung!");
    Serial.print("IP Address ESP32: ");
    Serial.println(WiFi.localIP());
    triggerFeedback(true, 2);
  } else {
    Serial.println("\n❌ [GAGAL] Belum terhubung ke WiFi. Tetap mencoba di background...");
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

  HTTPClient http;
  http.begin(API_URL);
  http.addHeader("Content-Type", "application/json");

  // Siapkan Payload JSON
  StaticJsonDocument<256> doc;
  doc["uid"] = uid;
  doc["device_id"] = DEVICE_ID;

  String requestBody;
  serializeJson(doc, requestBody);

  Serial.print("Mengirim POST ke: ");
  Serial.println(API_URL);

  int httpCode = http.POST(requestBody);

  if (httpCode > 0) {
    String response = http.getString();
    Serial.print("HTTP Status Code: ");
    Serial.println(httpCode);
    Serial.print("Respon Server: ");
    Serial.println(response);

    StaticJsonDocument<512> resDoc;
    DeserializationError err = deserializeJson(resDoc, response);

    if (!err) {
      const char* code = resDoc["code"] | "UNKNOWN";
      const char* message = resDoc["message"] | "";

      // SKENARIO 1: REGISTRASI SUKSES KE USER LOGIN
      if (String(code) == "REGISTRATION_SUCCESS") {
        Serial.println("🎉 [SUKSES REGISTRASI] Kartu e-KTP berhasil didaftarkan untuk user!");
        triggerFeedback(true, 3); // 3 bip merdu tanda sukses terdaftar
      }
      // SKENARIO 2: KARTU SUDAH TERDAFTAR DI AKUN LAIN (DITOLAK)
      else if (String(code) == "DUPLICATE_CARD") {
        Serial.println("⛔ [DITOLAK] KARTU SUDAH DIGUNAKAN DI AKUN LAIN! Tidak dapat didaftarkan.");
        triggerFeedback(false, 2); // 2 bip panjang tanda penolakan duplikat
      }
      // SKENARIO 3: AKSES / IDENTIFIKASI TAP DITERIMA
      else if (String(code) == "ACCESS_GRANTED") {
        Serial.print("✅ [AKSES DITERIMA] Selamat datang: ");
        Serial.println(resDoc["user"]["name"].as<const char*>());
        triggerFeedback(true, 1); // 1 bip konfirmasi akses diterima
      }
      // SKENARIO 4: KARTU BELUM TERDAFTAR
      else if (String(code) == "UNREGISTERED_CARD") {
        Serial.println("⚠️ [KARTU BELUM TERDAFTAR] Buka web SiRW lalu klik 'Scan e-KTP' untuk mendaftar.");
        triggerFeedback(false, 1);
      }
      else {
        Serial.println(message);
      }
    }
  } else {
    Serial.print("❌ [HTTP ERROR] Gagal menghubungi server SiRW. Error: ");
    Serial.println(http.errorToString(httpCode));
    triggerFeedback(false, 1);
  }

  http.end();
}
