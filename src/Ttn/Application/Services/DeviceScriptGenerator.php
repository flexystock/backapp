<?php

namespace App\Ttn\Application\Services;

class DeviceScriptGenerator
{
    public function generate(string $devEui, string $joinEui, string $appKey): string
    {
        $devEuiFormatted = $this->formatHexArray($devEui);
        $joinEuiFormatted = $this->formatHexArray($joinEui);
        $appKeyFormatted = $this->formatHexArray($appKey);

        return <<<SCRIPT
#include "LoRaWanMinimal_APP.h"
#include "Arduino.h"
#include "HX711.h"

#define HX_VCC GPIO4
#define HX_DT  GPIO2
#define HX_SCK GPIO1

// Claves OTAA
uint8_t devEui[] = { $devEuiFormatted };
uint8_t appEui[] = { $joinEuiFormatted };
uint8_t appKey[] = { $appKeyFormatted };

/* ABP para*/
/* ABP para*/
uint8_t nwkSKey[16] = { 0xE9, 0x72, 0xA7, 0xAE, 0x94, 0xF5, 0xD5, 0x3B, 0x8C, 0xC7, 0x14, 0xA4, 0x39, 0x21, 0xBE, 0x47 };
uint8_t appSKey[16] = { 0x2D, 0x79, 0x1C, 0x54, 0xF5, 0x11, 0x05, 0xE7, 0xF5, 0x9C, 0x7F, 0xE3, 0xA0, 0xE7, 0x50, 0xBD };
uint32_t devAddr =  ( uint32_t )0x260BBEB4;

uint16_t userChannelsMask[6]={ 0x00FF,0x0000,0x0000,0x0000,0x0000,0x0000 };
// HX711
HX711 bascula;
float factor_calibracion = 146405.27f;

TimerEvent_t sleepTimer;
bool sleepTimerExpired;

static void wakeUp() {
  sleepTimerExpired = true;
}

static void lowPowerSleep(uint32_t sleeptime) {
  sleepTimerExpired = false;
  TimerInit(&sleepTimer, &wakeUp);
  TimerSetValue(&sleepTimer, sleeptime);
  TimerStart(&sleepTimer);
  while (!sleepTimerExpired) lowPowerHandler();
  TimerStop(&sleepTimer);
}

void setup() {
  Serial.begin(115200);

  pinMode(HX_VCC, OUTPUT);
  digitalWrite(HX_VCC, HIGH);
  delay(500);

  bascula.begin(HX_DT, HX_SCK);
  bascula.set_scale(factor_calibracion);
  bascula.tare(20);

  if (ACTIVE_REGION == LORAMAC_REGION_AU915) {
    LoRaWAN.setSubBand2();
  }

  LoRaWAN.begin(LORAWAN_CLASS, ACTIVE_REGION);
  LoRaWAN.setAdaptiveDR(true);

  while (!LoRaWAN.isJoined()) {
    //Serial.println("Intentando unirse a la red...");
    LoRaWAN.joinOTAA(appEui, appKey, devEui);
    if (!LoRaWAN.isJoined()) {
      //Serial.println("Fallo de JOIN, durmiendo 30s...");
      lowPowerSleep(30000);
    }
  }
  //Serial.println("Dispositivo unido a TTN");
}

/*uint16_t getBatteryVoltage() {
  pinMode(VBAT_ADC_CTL, OUTPUT);
  digitalWrite(VBAT_ADC_CTL, HIGH);  // Enciende el divisor
  delay(1);                          // Espera mínima para estabilizar
  uint16_t voltage = analogReadmV(ADC);  // Lee voltaje en mV
  digitalWrite(VBAT_ADC_CTL, LOW);   // Apaga el divisor
  return voltage;
}*/

void loop() {
  lowPowerSleep(120000); // 2 minutos
  //lowPowerSleep(5000); // 2 minutos

  digitalWrite(HX_VCC, HIGH);
  delay(1000);
  //float peso_en_gramos = bascula.get_units(20);
  float peso_en_kilogramos = bascula.get_units(20); // Ya en kg
  digitalWrite(HX_VCC, LOW);

  //float peso_en_kilogramos = peso_en_gramos / 1000.0; // Convertir a kg

  // Preparar payload: kilogramos con dos decimales
  // Ejemplo: si peso_en_kilogramos es 1.00kg, peso_int_for_payload será 100
  uint16_t peso_int_for_payload = (uint16_t)(peso_en_kilogramos * 100);
  uint16_t voltaje = getBatteryVoltage();
  //Serial.printf("Voltaje leído (mV): %u\n", voltaje);  // <-- esta línea clave

  Serial.printf("Peso: %.2f kg, Voltaje: %u mV\n", peso_en_kilogramos, voltaje);

  uint8_t payload[4];
  payload[0] = peso_int_for_payload >> 8;
  payload[1] = peso_int_for_payload & 0xFF;
  payload[2] = voltaje >> 8;
  payload[3] = voltaje & 0xFF;

  //Serial.print("Bytes enviados: ");
  for (int i = 0; i < 4; i++) {
   //Serial.printf("%02X ", payload[i]);
  }
//Serial.println();

  if (LoRaWAN.send(4, payload, sizeof(payload), false)) {
    //Serial.println("Envio OK");
  } else {
    //Serial.println("Envio FALLIDO");
  }
}

void downLinkDataHandle(McpsIndication_t *mcpsIndication) {
  Serial.printf("Downlink recibido: %s, TAM %d, PUERTO %d, DATOS: ",
    mcpsIndication->RxSlot ? "RXWIN2" : "RXWIN1",
    mcpsIndication->BufferSize,
    mcpsIndication->Port);
  for (uint8_t i = 0; i < mcpsIndication->BufferSize; i++) {
    Serial.printf("%02X", mcpsIndication->Buffer[i]);
  }
  //Serial.println();
}
SCRIPT;
    }

    private function formatHexArray(string $hexString): string
    {
        $cleanHex = strtoupper(preg_replace('/[^0-9A-F]/', '', $hexString));

        if ($cleanHex === '') {
            throw new \InvalidArgumentException('Hex string cannot be empty.');
        }

        if (strlen($cleanHex) % 2 !== 0) {
            throw new \InvalidArgumentException('Hex string must contain an even number of characters.');
        }

        $bytes = str_split($cleanHex, 2);

        return implode(', ', array_map(static fn (string $byte) => '0x'.$byte, $bytes));
    }
}
