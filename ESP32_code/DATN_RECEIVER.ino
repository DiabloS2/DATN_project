//Board mạch ổ khóa cửa nằm tại các phòng riêng
//Dùng để đọc thẻ của khách hàng và mở/đóng cửa
//ESP32 library
#include <WiFi.h>
#include <HTTPClient.h>
#include <time.h>
//LCD library
#include <LiquidCrystal_I2C.h>
//KEYPAD library
#include <Keypad.h>
//Servo SG90 library
#include <ESP32Servo.h> 
//RFID MFRC522 library
#include <MFRC522.h>
#include <SPI.h>
//-------------------------------
#define onboard_led 4  //LED on board, turn on when connected to wifi
#define servo_pin 16   //SG90 also doorlock pin, RX2 pin on ESP32V1
#define Buzzer 15      //Buzzer making beep sound
#define OpenInside 32  //Open door button //Open door from inside 
#define LockInside 34  //Lock door button //Lock door from inside
#define LockOutside 35 //Lock door button //Lock door from outside
//Define RFID pins---------------
#define SS_PIN 17      //TX2 pin on ESP32 DEVKIT V1
#define SCK_PIN 5
#define MOSI_PIN 18
#define MISO_PIN 19
#define RST_PIN 23
//Define LCD+I2C pins------------
#define SDA_PIN 21
#define SCL_PIN 22
//Declare pin for keypad 3x4
const byte numRows= 4; //4 rows
const byte numCols= 3; //3 columns
char keymap[numRows][numCols] = 
{
{'1', '2', '3'}, 
{'4', '5', '6'}, 
{'7', '8', '9'},
{'*', '0', '#'}
};
byte rowPins[numRows] = {13,12,14,27};//D13//D12//D14//D27
byte colPins[numCols] = {26,25,33};   //D26//D25//D33
//----------------------------------
//SSID and Password of your WiFi router.
const char* ssid = "AP1234"; //Your wifi name or SSID.
const char* password = "12345678"; //Your wifi password.
//Web Server address / IPv4
//If using IPv4, press Windows + R -> cmd, then type "ipconfig".
const char* host = "http://192.168.43.107/"; //Your computer ipv4
String DataAddress = "rfid_hotel/getdata.php"; //PHP file
String Link = host + DataAddress;
String getData; 
String payload;   //HTTP return data (ID of card)
String RFIDdata;  //=payload if satisfy the condition
int httpCode = 0; //HTTP return code 
//----------------------------------
//Timer millis
unsigned long previousMillis = 0;
unsigned long interval = 10000;
//----------------------------------
//Boolean to change mode
//If scan_mode = false: you can enter number from keypad...
//...to declaring hotel room's numbers//Diplay on LCD16x2.
//If scan_mode = true: you can scan RFID card to enter the room
boolean scan_mode = false;
char keypressed; //Where the keys are stored it changes very often
unsigned int a=0, i=8; //Some variables
char room[3]; //Hotel room number array 
String room_number; //room_number = String(room)
//-------------------------
//LCD+I2C: SDA=D21//SCL=D22
LiquidCrystal_I2C lcd(0x27, 16, 2); //16 columns - 2 rows
//MFRC522: SCK=D5//MISO=D19//MOSI=D18//SS=D17//RST=D23
MFRC522 mfrc522(SS_PIN, RST_PIN); // Create MFRC522 instance.
Servo servo;
Keypad mykeypad= Keypad(makeKeymap(keymap), rowPins, colPins, numRows, numCols);
//-------------------------
//Get number typed from keypad 3x4 function
void GetSettingNumber() {
   i=8; //Position of first number from keypad display on LCD
   a=0; //First position of array room[3]
   for(;;){ //Infinity loop
     //If hotel room number don't have 3 digits number...
     //...you can't break the loop with * button
     if(keypressed == '*' && a == 3) break; 
     else {
       //Press * to confirm the numbers otherwise you can keep typing
       keypressed = mykeypad.getKey();      
       //If the char typed isn't * and # and neither "nothing"
       //If room[a] had 3 values -> can't typed from keypad
       //This code for make sure that hotel room number MUST HAVE 3 DIGIT
       if(keypressed != NO_KEY && keypressed != '*' && keypressed != '#' && a < 3){
         room[a] = keypressed;
         lcd.setCursor(i, 1); 
         lcd.print(keypressed); //Write the key pressed on LCD
         i++;
         a++;
       }
       if(keypressed == '#') { //Use # to delete whole array when mistyped
         room[0] = (char)0;
         memset(room, 0, sizeof(room)); //clear whole array
         i=8;
         a=0;
         lcd.clear();
         lcd.setCursor(0, 0);
         lcd.print("PLEASE SET ROOM");
         lcd.setCursor(0, 1);
         lcd.print("NUMBER:"); 
       }
     }    
   }
   keypressed = NO_KEY;
}
//Connect to wifi station function
void ConnectToWifi() { 
   digitalWrite(onboard_led, LOW);
   delay(500);
   digitalWrite(onboard_led, HIGH);
   delay(500);
   digitalWrite(onboard_led, LOW);
   unsigned long preTryCount = 0;
   unsigned long TryCount = millis();
   unsigned long timer = 10000;
   //Check wifi is connected or not   
   if ((WiFi.status() != WL_CONNECTED)&&(TryCount - preTryCount)>=timer){   
        WiFi.disconnect();
        WiFi.reconnect();
        preTryCount = TryCount;
   }    
}
//Receive data (UID card) from MYSQL server function
void ReceiveDataFromMysql() {
   HTTPClient http; //--> Declare object of class HTTPClient
   getData = "ID=" + room_number;
   http.begin(Link); //--> Specify request destination
   //Specify content-type header
   http.addHeader("Content-Type","application/x-www-form-urlencoded");
   httpCode = http.POST(getData); //--> Send the request
   payload = http.getString();    //--> Get the response payload from server
   Serial.println(httpCode);      //--> Print HTTP return code
   Serial.println(payload);       //--> Print request response payload 
   if (payload != NULL) {
       RFIDdata = payload;        //Make sure data is received  
   }
   http.end(); //--> Close connection
}
//Function for display LCD, not important :D
void LCDdisplay () {
   lcd.clear();
   lcd.setCursor(0, 0);
   lcd.print("ROOM NUMBER:");
   lcd.setCursor(13, 0);
   lcd.print(room_number);
}
//--------------------------------------
void setup() {  
   Serial.begin(115200);
   WiFi.mode(WIFI_STA);// Set station mode for esp32
   WiFi.begin(ssid, password);
   lcd.init();         // Initialize LCD                    
   lcd.backlight();    // Turn on LCD backlight    
   SPI.begin(SCK_PIN,MISO_PIN,MOSI_PIN);// Init SPI bus
   mfrc522.PCD_Init(); // Init MFRC522
   servo.attach(servo_pin, 500, 2400); 
   servo.write(0);
   delay(1000);
   //servo.detach();
   pinMode(OpenInside, INPUT_PULLUP);  //D32
   pinMode(LockInside, INPUT);         //D34
   pinMode(LockOutside, INPUT);        //D35
   pinMode(onboard_led, OUTPUT);       //D2
   pinMode(Buzzer, OUTPUT);            //D15
   delay(1000);
}
void OpenDoor() {
   servo.attach(servo_pin, 500, 2400); 
   servo.write(110);  //Open Door
   delay(500);       
}
void LockDoor() {
   servo.attach(servo_pin, 500, 2400); 
   servo.write(0);  //Lock Door
   delay(500);
}
//--------------------------------------
void loop() {
   if (digitalRead(OpenInside)==LOW)
       OpenDoor();
   if (digitalRead(LockInside)==LOW || digitalRead(LockOutside)==LOW) {
       LockDoor();             
   }
   if (scan_mode == false) {
       lcd.clear();
       lcd.setCursor(0, 0);
       lcd.print("PLEASE SET ROOM");
       lcd.setCursor(0, 1);
       lcd.print("NUMBER:");      
       GetSettingNumber();//Getting room number function from keypad      
       room_number = String(room);
       Serial.print(room_number);
       scan_mode = true;  //change mode to scan RFID card
       LCDdisplay();      //Function
   }
   if (scan_mode == true) {      
       //check if there's a connection to Wi-Fi or not
       switch (WiFi.isConnected()) {
          case true: 
             //Turn on led D2 when Wifi is connected
             digitalWrite(onboard_led, HIGH);
             break;
          case false: 
             ConnectToWifi(); //Connect to wifi when lost connection    
             break; 
       }      
       unsigned long delay_time = millis() - previousMillis;
       if (httpCode != 200 || delay_time >= interval) {
          ReceiveDataFromMysql(); //Receive data from MYSQL server          
          previousMillis = millis();          
       }
       //-----------------                                  
       //Looking for new card
       if ( !mfrc522.PICC_IsNewCardPresent()) {
          return;//got to start of loop if there is no card present
       }
       if ( !mfrc522.PICC_ReadCardSerial()) {
          return;//if read card serial(0) returns 1, the uid struct contians the ID of the read card.
       }
       delay(200);
       tone(Buzzer, 900, 400);              
       String CardID ="";
       for (byte j = 0; j < mfrc522.uid.size; j++) {
          CardID += mfrc522.uid.uidByte[j];
       }
       //Compare two ID string: ID get from webserver and ID get from card scan by customer.
       Serial.println(CardID);      
       if (CardID == RFIDdata) {         
          lcd.setCursor(2, 1);
          lcd.print("CARD MATCHED");          
          delay(1000);
          OpenDoor();
          LCDdisplay();      
          Serial.println("Door is open");
       } else {                   
          lcd.setCursor(3, 1);
          lcd.print("WRONG CARD");          
          delay(1000);
          LockDoor();
          LCDdisplay();      
          Serial.println("Door is close");
       }       
   }
}
