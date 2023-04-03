//Code cho board mạch nằm tại bàn tiếp tân
//Dùng để tạo thẻ cho khách hàng mới đến khách sạn
//ESP32 library
#include <WiFi.h>
#include <HTTPClient.h>
#include <time.h>
//RFID MFRC522 library
#include <MFRC522.h>
#include <SPI.h>
//---------------------------------
#define onboard_led 2  //LED on board, turn on when connected to wifi
#define red_led 21
#define green_led 22
//Define RFID pins---------------
#define SS_PIN 17      //TX2 pin on ESP32 DEVKIT V1
#define SCK_PIN 5
#define MOSI_PIN 18
#define MISO_PIN 19
#define RST_PIN 23
//----------------------------------
const int tone_output_pin = 15;   //Buzzer making beep sound
const int tone_pwm_channel = 0 ;  //Choose PWM channel 0
//----------------------------------
//SSID and Password of your WiFi router.
const char* ssid = "boi"; //Your wifi name or SSID.
const char* password = "thepot654321"; //Your wifi password.
//Web Server address / IPv4
//If using IPv4, press Windows + R -> cmd, then type "ipconfig".
const char* host = "http://192.168.137.1/"; //Your computer ipv4
String DataAddress = "rfid_hotel/getdata.php"; //PHP file
String Link = host + DataAddress;
String getData;
String OldCardID = "";
unsigned long previousMillis = 0;
//MFRC522: SCK=D5//MISO=D19//MOSI=D18//SS=D17//RST=D23
MFRC522 mfrc522(SS_PIN, RST_PIN); // Create MFRC522 instance.
//-----------------------------------
//Connect to wifi station function
void ConnectToWifi() {   
   WiFi.begin(ssid, password);
   unsigned int TryCount = 1;
   //Check wifi is connected or not   
   while (WiFi.status() != WL_CONNECTED) {   
      digitalWrite(onboard_led, HIGH);
      delay(500);
      digitalWrite(onboard_led, LOW);
      delay(500);
      TryCount++;
      //Reconnect to wifi each 8 second    
      if ((unsigned int)(TryCount%8) == 0) {
         WiFi.disconnect();
         WiFi.begin(ssid, password);
      }    
   }
   Serial.println("Connected to wifi"); 
}
void setup() {
   Serial.begin(115200);
   WiFi.mode(WIFI_STA);// Set station mode for esp32
   WiFi.disconnect();  // Disconnect from an AP if it was previously connected
   delay(100);
   SPI.begin(SCK_PIN,MISO_PIN,MOSI_PIN);// Init SPI bus
   mfrc522.PCD_Init(); // Init MFRC522
   pinMode(onboard_led, OUTPUT);   //D2
   pinMode(green_led, OUTPUT);     //D22
   pinMode(red_led, OUTPUT);       //D21
   digitalWrite(onboard_led, LOW);
   digitalWrite(green_led, LOW);
   digitalWrite(red_led, LOW);
   //ledcSetup(PWM_Ch, PWM_Freq, PWM_Res);
   ledcSetup(tone_pwm_channel, 1000, 12);
   //ledcAttachPin(uint8_t pin, uint8_t channel);
   ledcAttachPin(tone_output_pin, tone_pwm_channel);
}
void loop() {
   switch (WiFi.isConnected()) {
      case true: 
         //Turn on led D2 when Wifi is connected
         digitalWrite(onboard_led, HIGH);
         break;
      case false:
         digitalWrite(onboard_led, LOW);
         ConnectToWifi(); //Reconnect to wifi when lost connection
         break; 
   }
   if ((unsigned long)(millis() - previousMillis) >= 30000) {
      previousMillis = millis();
      OldCardID="";
   }
   //RFID part
   //Look for new card
   if ( ! mfrc522.PICC_IsNewCardPresent()) {
      return;//got to start of loop if there is no card present
   }
   //Select one of the cards
   if ( ! mfrc522.PICC_ReadCardSerial()) {
      return;//if read card serial(0) returns 1, the uid struct contians the ID of the read card.
   }
   ledcWriteTone(tone_pwm_channel, 400); 
   delay(400);
   String CardID ="";
   for (byte i = 0; i < mfrc522.uid.size; i++) {
      CardID += mfrc522.uid.uidByte[i];
   }
   OldCardID = CardID;
   SendCardID(CardID);
   delay(1000);   
}
void SendCardID(String Card_ID){
   Serial.println("Sending the Card ID");
   if(WiFi.isConnected()){
      HTTPClient http; //--> Declare object of class HTTPClient
      getData = "?card_uid=" + String(Card_ID);//Add the CardID to the GET array to send it      
      http.begin(Link + getData); //Specify request destination
      int httpCode = http.GET();  //Send the request
      String payload = http.getString(); //Get the response payload
      Serial.println(httpCode);   //Print HTTP return code
      Serial.println(Card_ID);    //Print Card ID
      Serial.println(payload);    //Print request response payload
      http.end();                 //Close connection
      if (httpCode == 200) {
         Serial.println("ID was sent successful");
         Serial.println("----------------------");
         digitalWrite(green_led, HIGH);
         delay(1000);
         digitalWrite(green_led, LOW);
      } else {
         Serial.println("ID was sent fail");
         Serial.println("----------------------");
         digitalWrite(red_led, HIGH);
         delay(1000);
         digitalWrite(red_led, LOW);
      }
   }    
}
