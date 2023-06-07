#include <ESP8266WiFi.h>
#include <LiquidCrystal_I2C.h>
LiquidCrystal_I2C lcd(0x27, 16, 2);

const char* ssid     = "testing";
const char* password = "12345687";

const char* host = "192.168.147.30";

WiFiClient client;
const int httpPort = 80;

int myBuzzer = 0;

unsigned long timeout;

void setup() {
  Serial.begin(9600);
  pinMode(myBuzzer,OUTPUT) ;
  delay(10);

  lcd.begin();
 
  // Nyalakan backlight
  lcd.backlight();
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(100);
    Serial.println(".");
  }
}


void loop() {
  String payload = "";
  const int httpPort = 80;
  if (!client.connect(host, httpPort)) {
   Serial.println("connection failed");
   return;
  }

  String url = "http://localhost/wahana/testing.txt";
         
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  unsigned long timeout = millis();
  while (client.available() == 0) {
   if (millis() - timeout > 5000) {
    Serial.println(">>> Client Timeout !");
    client.stop();
    return;
   }
  }

  while(client.available()){
   
   String req = client.readStringUntil('\n');

   int indexawal = req.indexOf("=>");
   int indexakhir = req.indexOf("</b>"); 
   Serial.print(req.substring(indexawal, indexakhir));
   lcd.setCursor(0,0);
   lcd.print(req.substring(indexawal, indexakhir));


//   if(client.find("<b>=> Wahana Bermain</b>")) {
//     digitalWrite(myBuzzer,LOW);
//     delay (300);
//     digitalWrite(myBuzzer,LOW);
//     delay (300);  
//   }
//   else {
     digitalWrite(myBuzzer,HIGH);
     delay (300);
     digitalWrite(myBuzzer,LOW);
     delay (300);  
//   }
   
   delay(100);
  }

  Serial.println();
}
