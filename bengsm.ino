#include <WiFi.h>
#include <HTTPClient.h>

#define TINY_GSM_MODEM_SIM800
#include <TinyGsmClient.h>

#define TRIG_PIN 5
#define ECHO_PIN 18
#define LED_PIN 21
#define BUZZER_PIN 22

const char* ssid = "Your_WiFi_Name";
const char* password = "Your_WiFi_Password";

String serverName = "http://your-server.com/update";

// GSM using HardwareSerial (IMPORTANT FIX)
HardwareSerial SerialGSM(1);
TinyGsm modem(SerialGSM);

void setup() {

  Serial.begin(115200);

  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  pinMode(LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);

  // GSM UART (TX=27 RX=26)
  SerialGSM.begin(9600, SERIAL_8N1, 26, 27);
  modem.restart();

  WiFi.begin(ssid, password);

  Serial.print("Connecting WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi Connected");
}

void loop() {

  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);

  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  long duration = pulseIn(ECHO_PIN, HIGH);
  int distance = duration * 0.034 / 2;

  Serial.println(distance);

  if (distance <= 45) {
    digitalWrite(LED_PIN, HIGH);
    modem.sendSMS("+2507XXXXXXX", "Alert: Distance < 45cm");
  } else {
    digitalWrite(LED_PIN, LOW);
  }

  if (distance >= 95) {
    digitalWrite(BUZZER_PIN, HIGH);
    modem.sendSMS("+2507XXXXXXX", "Critical: Distance >= 95cm");
  } else {
    digitalWrite(BUZZER_PIN, LOW);
  }

  if (WiFi.status() == WL_CONNECTED) {

    HTTPClient http;

    String url = serverName + "?distance=" + String(distance);

    http.begin(url);

    int code = http.GET();

    Serial.println(code);

    http.end();
  }

  delay(1000);
}