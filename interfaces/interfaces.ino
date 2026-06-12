#include "sgp30.h"

#define SW1_PIN PF_4
#define SW2_PIN PF_0
#define LED_R_PIN PF_1
#define LED_G_PIN PF_3
#define LED_B_PIN PF_2

#define MQ2_PIN A3

static unsigned long lastWake = 0;
static unsigned long lastFetch = 0;
static unsigned short co2, ch4, voc;
static char co2_output[128];
static char ch4_output[128];
static char voc_output[128];
static bool isWarming = true;
static bool isAwake = true;
static bool isScreenCleared = false;

void setup() {
	pinMode(SW1_PIN, INPUT_PULLUP);

	AOLED_InitScreen();
	AOLED_FillScreen(0);
	AOLED_DisplayTexte(8, 1, "SmartWake");

	while (sgp_probe() != STATUS_OK) {
		delay(1000);
	}
	sgp_iaq_init();

	u16 scaled_ethanol_signal, scaled_h2_signal;
	sgp_measure_signals_blocking_read(&scaled_ethanol_signal, &scaled_h2_signal);

	Serial.begin(9600);

	pinMode(LED_R_PIN, OUTPUT);
	pinMode(LED_G_PIN, OUTPUT);
	pinMode(LED_B_PIN, OUTPUT);

	// indicate sensor warm-up
	digitalWrite(LED_R_PIN, HIGH);
}

void loop() {
	if (isWarming) {
		sgp_measure_iaq_blocking_read(&voc, &co2);
		if (co2 != 400) {
			isWarming = false;
		}
		return;
	}

	digitalWrite(LED_R_PIN, LOW);
	digitalWrite(LED_G_PIN, LOW);
	digitalWrite(LED_B_PIN, LOW);
	if (!digitalRead(SW1_PIN)) {
		lastWake = millis();
		AOLED_DisplayTexte(8, 1, "SmartWake");
		digitalWrite(LED_G_PIN, HIGH);
		isScreenCleared = false;
	}

	isAwake = millis() - lastWake > 60000 ? false : true;

	if (millis() - lastFetch > 1000) {
		lastFetch = millis();

		sgp_measure_iaq_blocking_read(&voc, &co2);
		ch4 = analogRead(MQ2_PIN) + 1;

		sprintf(co2_output, "CO2: %d ppm", co2);
		sprintf(ch4_output, "CH4: %d ppb", ch4);
		sprintf(voc_output, "VOC: %d ppb", voc);

		Serial.println(co2);
		Serial.println(ch4);
		Serial.println(voc);

		if (isAwake) {
			for (int line = 3; line <=5; ++line) { AOLED_FillLine(line, 0x0); }
			AOLED_DisplayTexte(8, 3, co2_output);
			AOLED_DisplayTexte(8, 4, ch4_output);
			AOLED_DisplayTexte(8, 5, voc_output);
		} else if (!isScreenCleared) {
			AOLED_FillScreen(0x0);
			isScreenCleared = true;
		}
		digitalWrite(LED_B_PIN, HIGH);
	}
	delay(30);
}