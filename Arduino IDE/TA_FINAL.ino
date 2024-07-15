#define BLYNK_TEMPLATE_ID "TMPL66b-buG9w"
#define BLYNK_TEMPLATE_NAME "Monitoring Listrik"
#define BLYNK_AUTH_TOKEN "f3xiBla2UmCX2klP4VuzaYjXZlvyulJZ"

#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <PZEM004Tv30.h>
#include <WiFiManager.h>
#include <BlynkSimpleEsp32.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <FS.h>
#include <ArduinoOTA.h>
#include <EEPROM.h>

// Define constants
#define BLYNK_TEMPLATE_ID_LEN 20
#define BLYNK_TEMPLATE_NAME_LEN 40
#define BLYNK_AUTH_TOKEN_LEN 40
#define MAX_LEN 40

char ssid[50];
char pass[50];
char lokasi_id[MAX_LEN];
char blynk_template_id[BLYNK_TEMPLATE_ID_LEN];
char blynk_template_name[BLYNK_TEMPLATE_NAME_LEN];
char blynk_auth_token[BLYNK_AUTH_TOKEN_LEN];

char created_at[50];
const char* ntpServer = "pool.ntp.org";
const long gmtOffset_sec = 25200;
const int daylightOffset_sec = 0;

LiquidCrystal_I2C lcd(0x27, 16, 2);
PZEM004Tv30 pzem(Serial2, 16, 17);
float tarifPerKWh = 1352;
const int relayPin = 5;
const int buzzerPin = 15; 
int relayState = LOW;
WiFiClient client;
IPAddress serverAddr;

unsigned long lastUploadTime = 0; // Variabel untuk menyimpan waktu upload terakhir
const unsigned long uploadInterval = 60000; // Interval 1 menit (60000 ms)
unsigned long lastBlynkUpdateTime = 0; // Variabel untuk menyimpan waktu update Blynk terakhir
const unsigned long blynkUpdateInterval = 1000; // Interval 1 detik (1000 ms)
unsigned long previousMillis = 0;   // Waktu sebelumnya
const long buzzerDuration = 60000;  // Durasi buzzer dalam milidetik (1 menit = 60 detik = 60000 milidetik)

void setup() {
  WiFi.mode(WIFI_STA);
  Serial.begin(115200);
  EEPROM.begin(512);
  pinMode(relayPin, OUTPUT);
  pinMode(buzzerPin, OUTPUT);
  
  // Membaca data dari EEPROM
  String read_lokasi_id = EEPROM.readString(0);
  String read_blynk_template_id = EEPROM.readString(20);
  String read_blynk_template_name = EEPROM.readString(60);
  String read_blynk_auth_token = EEPROM.readString(100);

  // Menampilkan data yang dibaca di Serial Monitor
  Serial.println("Reading from EEPROM:");
  Serial.println("lokasi_id: " + read_lokasi_id);
  Serial.println("blynk_template_id: " + read_blynk_template_id);
  Serial.println("blynk_template_name: " + read_blynk_template_name);
  Serial.println("blynk_auth_token: " + read_blynk_auth_token);

  // Inisialisasi WiFiManager
  WiFiManager wifiManager;

  bool needConfiguration = false;

  // Memeriksa apakah data EEPROM valid
  if (read_lokasi_id.length() > 0 && read_blynk_template_id.length() > 0 &&
      read_blynk_template_name.length() > 0 && read_blynk_auth_token.length() > 0) {
    // Data valid, gunakan untuk konfigurasi Blynk
    strncpy(lokasi_id, read_lokasi_id.c_str(), sizeof(lokasi_id));
    strncpy(blynk_template_id, read_blynk_template_id.c_str(), sizeof(blynk_template_id));
    strncpy(blynk_template_name, read_blynk_template_name.c_str(), sizeof(blynk_template_name));
    strncpy(blynk_auth_token, read_blynk_auth_token.c_str(), sizeof(blynk_auth_token));

    Serial.println("Using saved data from EEPROM:");
    Serial.println("lokasi_id: " + String(lokasi_id));
    Serial.println("blynk_template_id: " + String(blynk_template_id));
    Serial.println("blynk_template_name: " + String(blynk_template_name));
    Serial.println("blynk_auth_token: " + String(blynk_auth_token));

    // Coba hubungkan ke WiFi dengan SSID yang disimpan
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Connecting to");
    lcd.setCursor(0, 1);
    lcd.print("WiFi.........");
    delay(5000);
    WiFi.begin(ssid, pass);
    if (WiFi.waitForConnectResult() != WL_CONNECTED) {
      Serial.println("Failed to connect to WiFi with saved credentials. Opening configuration portal...");
      needConfiguration = true;
    } else {
      Serial.println("Connected to WiFi with saved credentials");

      // Periksa apakah SSID telah berubah
      if (String(WiFi.SSID()) != String(ssid)) {
        Serial.println("SSID has changed, opening configuration portal...");
        needConfiguration = true;
      }
    }
  } else {
    // Data tidak valid atau kosong, buka portal konfigurasi
    Serial.println("No saved data in EEPROM or data invalid. Opening configuration portal...");
    needConfiguration = true;
  }

  if (needConfiguration) {
    WiFiManagerParameter custom_lokasi_id("lokasi_id", "lokasi ID", lokasi_id, MAX_LEN);
    WiFiManagerParameter custom_blynk_template_id("blynk_template_id", "Blynk Template ID", blynk_template_id, BLYNK_TEMPLATE_ID_LEN);
    WiFiManagerParameter custom_blynk_template_name("blynk_template_name", "Blynk Template Name", blynk_template_name, BLYNK_TEMPLATE_NAME_LEN);
    WiFiManagerParameter custom_blynk_auth_token("blynk_auth_token", "Blynk Auth Token", blynk_auth_token, BLYNK_AUTH_TOKEN_LEN);

    wifiManager.addParameter(&custom_lokasi_id);
    wifiManager.addParameter(&custom_blynk_template_id);
    wifiManager.addParameter(&custom_blynk_template_name);
    wifiManager.addParameter(&custom_blynk_auth_token);

    if (!wifiManager.autoConnect("VoltTech", "password")) {
      Serial.println("Failed to connect to WiFi and hit timeout");
      ESP.restart();
      delay(1000);
    }

    // Simpan parameter kustom ke variabel
    strncpy(ssid, WiFi.SSID().c_str(), sizeof(ssid));
    strncpy(pass, WiFi.psk().c_str(), sizeof(pass));
    strncpy(lokasi_id, custom_lokasi_id.getValue(), sizeof(lokasi_id));
    strncpy(blynk_template_id, custom_blynk_template_id.getValue(), sizeof(blynk_template_id));
    strncpy(blynk_template_name, custom_blynk_template_name.getValue(), sizeof(blynk_template_name));
    strncpy(blynk_auth_token, custom_blynk_auth_token.getValue(), sizeof(blynk_auth_token));

    // Simpan parameter kustom ke EEPROM
    EEPROM.writeString(0, lokasi_id);
    EEPROM.writeString(20, blynk_template_id);
    EEPROM.writeString(60, blynk_template_name);
    EEPROM.writeString(100, blynk_auth_token);
    EEPROM.commit();

    // Tampilkan data yang ditulis ke EEPROM di Serial Monitor
    Serial.println("Saved new data to EEPROM:");
    Serial.println("lokasi_id: " + String(lokasi_id));
    Serial.println("blynk_template_id: " + String(blynk_template_id));
    Serial.println("blynk_template_name: " + String(blynk_template_name));
    Serial.println("blynk_auth_token: " + String(blynk_auth_token));
  }

  // Initialize Blynk with obtained credentials
  Blynk.begin(blynk_auth_token, ssid, pass);

  // Menampilkan data yang digunakan ke Serial Monitor
  Serial.println("Final data used:");
  Serial.println("lokasi_id: " + String(lokasi_id));
  Serial.println("blynk_template_id: " + String(blynk_template_id));
  Serial.println("blynk_template_name: " + String(blynk_template_name));
  Serial.println("blynk_auth_token: " + String(blynk_auth_token));
  
  // Inisialisasi OTA
  ArduinoOTA.begin();
  buzzTwice();
  uploadWireless();

  lcd.init();
  lcd.backlight();
  lcd.print("   kWh-Meter   ");
  delay(2000);
  lcd.clear();

  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);
    // Ensure time is properly set up
  // Wait for time to be set
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Failed to obtain time");
  } else {
    Serial.println("Time obtained successfully");
    Serial.print("Current time: ");
    Serial.printf("%02d-%02d-%04d %02d:%02d:%02d\n",
                  timeinfo.tm_mday,
                  timeinfo.tm_mon + 1,
                  timeinfo.tm_year + 1900,
                  timeinfo.tm_hour,
                  timeinfo.tm_min,
                  timeinfo.tm_sec);
  }
}

void loop() {
  ArduinoOTA.handle();
  Blynk.run();
  getDateTime();
  unsigned long currentMillis = millis();

  // Pernyataan debugging untuk memeriksa status relayState saat ini
  Serial.print("relayState: ");
  Serial.println(relayState);
  if (relayState == HIGH) { // Mode Otomatis Aktif
    controlRelayBasedOnSensor(); // Gunakan logika kontrol otomatis jika mode Otomatis
  } else { // Mode Otomatis Nonaktif
    digitalWrite(relayPin, LOW); // Matikan relay
  }

  float voltage = pzem.voltage();
  float current = pzem.current();
  float powerFactor = pzem.pf();
  float power = voltage * powerFactor * current;
  float hours = 1;
  float energy = power * (hours / 1000);

  if (power >= 450) { // Aktifkan buzzer jika daya lebih besar dari atau sama dengan 5 W
    digitalWrite(buzzerPin, HIGH);
    if (currentMillis - previousMillis >= buzzerDuration) {
      digitalWrite(buzzerPin, LOW);  // Matikan buzzer setelah 1 menit
      previousMillis = currentMillis;  // Perbarui waktu sebelumnya
    }
  } else {
    digitalWrite(buzzerPin, LOW);
  }

  Serial.println("Pembacaan Sensor:");
  Serial.print("Voltage: "); Serial.print(voltage); Serial.println(" V");
  Serial.print("Current: "); Serial.print(current); Serial.println(" A");
  Serial.print("Power Factor: "); Serial.println(powerFactor);
  Serial.print("Power: "); Serial.print(power); Serial.println(" W");
  Serial.print("Energy: "); Serial.print(energy); Serial.println(" kWh");
  Serial.println();

  if (isnan(voltage) || isnan(current) || isnan(powerFactor)) {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Error membaca");
    lcd.setCursor(0, 1);
    lcd.print("sensor");
  } else {
    float biaya = (energy * tarifPerKWh);

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Voltage: "); lcd.print(voltage); lcd.print("V");
    lcd.setCursor(0, 1);
    lcd.print("Power: "); lcd.print(power); lcd.print("W");
    delay(2000);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Pf: "); lcd.print(powerFactor);
    lcd.setCursor(0, 1);
    lcd.print("Energy: "); lcd.print(energy); lcd.print("kWh");
    delay(2000);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Current: "); lcd.print(current); lcd.print("A");
    lcd.setCursor(0, 1);
    lcd.print("Biaya: Rp. "); lcd.print(biaya, 2);
    delay(2000);

    unsigned long currentTime = millis();
    if (currentTime - lastBlynkUpdateTime >= blynkUpdateInterval) {
      // Upload data ke Blynk
      Blynk.virtualWrite(V2, voltage);
      Blynk.virtualWrite(V3, current);
      Blynk.virtualWrite(V4, powerFactor);
      Blynk.virtualWrite(V5, power);
      Blynk.virtualWrite(V6, energy);
      Blynk.virtualWrite(V7, biaya);
      lastBlynkUpdateTime = currentTime;
    }

    if (currentTime - lastUploadTime >= uploadInterval) {
      int lokasi_id_int = atoi(lokasi_id);
      // Upload data ke server
      sendHttpPost(lokasi_id_int, voltage, power, powerFactor, energy, current, biaya, created_at); 
      lastUploadTime = currentTime;
    }
  }
}

void controlRelayBasedOnSensor() {
  float power = pzem.power();
  if (power >= 500) {
    digitalWrite(relayPin, LOW); // Matikan relay jika daya lebih dari atau sama dengan 20 W
    relayState = LOW; // Simpan status relay
  } else {
    digitalWrite(relayPin, HIGH); // Hidupkan relay jika daya kurang dari 20 W
    relayState = HIGH; // Simpan status relay
  }
}

void sendHttpPost(int lokasi_id, float voltage, float power, float powerFactor, float energy, float current, float biaya, const char* created_at) {
  if (WiFi.status() == WL_CONNECTED) { // Pastikan WiFi terhubung
    HTTPClient http;
    Serial.println("Memulai HTTP POST...");

    http.begin("https://kantorku.cloud/voltech/upload/sensor_data.php");
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<200> jsonDoc;
    jsonDoc["lokasi_id"] = lokasi_id;
    jsonDoc["voltage"] = voltage;
    jsonDoc["power"] = power;
    jsonDoc["power_factor"] = powerFactor;
    jsonDoc["energy"] = energy;
    jsonDoc["current"] = current;
    jsonDoc["biaya"] = biaya;
    jsonDoc["created_at"] = created_at;

    String jsonString;
    serializeJson(jsonDoc, jsonString);

    // Debugging: Cetak payload JSON
    Serial.print("Payload JSON: ");
    Serial.println(jsonString);

    // Kirim permintaan POST dengan payload JSON
    int httpResponseCode = http.POST(jsonString);

    if (httpResponseCode > 0) {
        Serial.print("Kode Respons HTTP: ");
        Serial.println(httpResponseCode);
        String response = http.getString();
        Serial.println("Respons dari server: " + response);
    } else {
        Serial.print("Kode Kesalahan: ");
        Serial.println(httpResponseCode);
    }


    http.end();
  } else {
    Serial.println("WiFi tidak terhubung");
  }
}

void uploadWireless() {
  ArduinoOTA
    .onStart([]() {
      String type;
      if (ArduinoOTA.getCommand() == U_FLASH)
        type = "sketch";
      else // U_SPIFFS
        type = "filesystem";

      Serial.println("Start updating " + type);
    })
    .onEnd([]() {
      Serial.println("\nEnd");
    })
    .onProgress([](unsigned int progress, unsigned int total) {
      Serial.printf("Progress: %u%%\r", (progress / (total / 100)));
    })
    .onError([](ota_error_t error) {
      Serial.printf("Error[%u]: ", error);
      if (error == OTA_AUTH_ERROR) Serial.println("Auth Failed");
      else if (error == OTA_BEGIN_ERROR) Serial.println("Begin Failed");
      else if (error == OTA_CONNECT_ERROR) Serial.println("Connect Failed");
      else if (error == OTA_RECEIVE_ERROR) Serial.println("Receive Failed");
      else if (error == OTA_END_ERROR) Serial.println("End Failed");
    });

  ArduinoOTA.begin();

  Serial.println("Ready");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void getDateTime() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Failed to obtain time");
    snprintf(created_at, sizeof(created_at), "0000-00-00 00:00:00");
  } else {
    snprintf(created_at, sizeof(created_at), "%04d-%02d-%02d %02d:%02d:%02d",
             timeinfo.tm_year + 1900,
             timeinfo.tm_mon + 1,
             timeinfo.tm_mday,
             timeinfo.tm_hour,
             timeinfo.tm_min,
             timeinfo.tm_sec);
  }
}

BLYNK_WRITE(V1) {
  relayState = param.asInt(); // Mengubah status relay berdasarkan tombol Blynk V1
  Serial.print("Relay state changed to: ");
  Serial.println(relayState);
  // Balikkan logika relay untuk modul relay aktif rendah
  if (relayState == 1) {
    digitalWrite(relayPin, LOW); // Hidupkan relay jika tombol Blynk diaktifkan
  } else {
    digitalWrite(relayPin, HIGH); // Matikan relay jika tombol Blynk dinonaktifkan
  }
}

void buzzTwice() {
  for (int i = 0; i < 2; i++) {
    digitalWrite(buzzerPin, HIGH);
    delay(200);
    digitalWrite(buzzerPin, LOW);
    delay(200);
  }
}
