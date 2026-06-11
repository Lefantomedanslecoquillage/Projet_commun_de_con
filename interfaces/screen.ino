#include "inc/hw_memmap.h"
#include "inc/hw_types.h"

#include "driverlib/i2c.h"
#include "driverlib/gpio.h"
#include "driverlib/pin_map.h"
#include "driverlib/sysctl.h"

#include "ISEP_Font.h"

// L'afficheur est connecté au port I2C2
#define I2C_NUM 2

// L'adresse est 0x78 sur 8 bits
#define AFIOLED_ADDR 0x78

extern const char Isep_Font[];

static void TIVA_I2C2_InitModule(short i2cmun);
static void TIVA_I2C2_SendData(char slaveAdr, short nbdata, char* pBuff);

static void AOLED_WriteCmde(char cmde) {
	char Buff[4];
	Buff[0] = 0x00;  // 0x00 indique le mode commande
	Buff[1] = cmde;
	TIVA_I2C2_SendData(AFIOLED_ADDR, 2, Buff);
}

static void AOLED_WriteData(char data) {
	char Buff[4];
	Buff[0] = 0x40;  // 0x40 indique le mode commande
	Buff[1] = data;
	TIVA_I2C2_SendData(AFIOLED_ADDR, 2, Buff);
}

static void AOLED_SetAdrsLin(char addrs) {
	addrs = 0xB0 | (addrs & 0x07);
	AOLED_WriteCmde(addrs);
}

static void AOLED_SetAdrsCol(char addrs) {
	char msb, lsb;
	addrs += 2;
	msb = 0x10 | (addrs >> 4);	 lsb = addrs & 0x0F;
	AOLED_WriteCmde(msb);
	AOLED_WriteCmde(lsb);
}

void AOLED_InvertDisplay(short invert) {
	char cmd = invert ? 0xA7 : 0xA6;
	AOLED_WriteCmde(cmd);
}

void AOLED_FillLine(short numlin, char value) {
	short numcol;

	AOLED_SetAdrsLin(numlin);
	AOLED_SetAdrsCol(0);
	for (numcol = 0; numcol < 128; numcol++) {
		AOLED_WriteData(value);
	}
}

void AOLED_FillScreen(char value) {
	short numcol, numlin;

	for (numlin = 0; numlin < 8; numlin++) {
		AOLED_SetAdrsLin(numlin);
		AOLED_SetAdrsCol(0);
		for (numcol = 0; numcol < 128; numcol++) {
			AOLED_WriteData(value);
		}
	}
}

void AOLED_WriteColonne(char value, short nbcol) {
	short idc;

	if (nbcol > 5)
		nbcol = 5;
	for (idc = 0; idc < nbcol; idc++) {
		AOLED_WriteData(value);
	}
}

void AOLED_DisplayImage(const char *pBuff, int stLin, int edLin) {
	short numcol, numlin, deblin;
	deblin = 0;
	for (numlin = stLin; numlin < edLin; numlin++) {
		AOLED_SetAdrsLin(numlin);
		AOLED_SetAdrsCol(0);
		for (numcol = 127; numcol >= 0; numcol--) {
			AOLED_WriteData(pBuff[deblin + numcol]);
		}
		deblin += 128;
	}
}

void AOLED_DisplayCarac(short numcol, short numlin, char car) {
	short ndc, debcar;
	char valcol;

	AOLED_SetAdrsLin(numlin);
	AOLED_SetAdrsCol(128 - numcol);
	debcar = (car - 0x20) * 5;	  // 1 car = 5 col / 8 lin
	for (ndc = 4; ndc >= 0; ndc--) {
		valcol = Isep_Font[debcar + ndc];
		AOLED_WriteData(valcol << 1);
	}
}

void AOLED_DisplayTexte(short numcol, short numlin, char *texte) {
	while (*texte) {
		AOLED_DisplayCarac(numcol, numlin, *texte);
		numcol += 6;
		texte++;
	}
}

void AOLED_InitScreen(void) {
	TIVA_I2C2_InitModule(I2C_NUM);
	delay(1000);

	AOLED_WriteCmde(0xAE);	 // Set display OFF

	AOLED_WriteCmde(0xD4);	 // Set Display Clock Divide Ratio / OSC Frequency
	AOLED_WriteCmde(0x80);	 // Display Clock Divide Ratio / OSC Frequency

	AOLED_WriteCmde(0xA8);	 // Set Multiplex Ratio
	AOLED_WriteCmde(0x3F);	 // Multiplex Ratio for 128x64 (64-1)

	AOLED_WriteCmde(0xD3);	 // Set Display Offset
	AOLED_WriteCmde(0x00);	 // Display Offset

	AOLED_WriteCmde(0x40);	 // Set Display Start Line 0

	AOLED_WriteCmde(0x8D);	 // Set Charge Pump
	AOLED_WriteCmde(0x14);	 // Charge Pump (0x10 External, 0x14 Internal DC/DC)

	AOLED_WriteCmde(0xA0);	 // Set Segment Re-Map

	AOLED_WriteCmde(0xC8);	 // Set Com Output Scan Direction = inverse = AFFI RETOURNE
  //AOLED_WriteCmde(0xC0);	 // Set Com Output Scan Direction = normal	//BB_MODIF

	AOLED_WriteCmde(0xDA);	 // Set COM Hardware Configuration
	AOLED_WriteCmde(0x12);	 // COM Hardware Configuration

	AOLED_WriteCmde(0x81);	 // Set Contrast
	AOLED_WriteCmde(0xCF);	 // Contrast

	AOLED_WriteCmde(0xD9);	 // Set Pre-Charge Period
	AOLED_WriteCmde(0xF1);	 // Set Pre-Charge Period (0x22 External, 0xF1 Internal)

	AOLED_WriteCmde(0xDB);	 // Set VCOMH Deselect Level
	AOLED_WriteCmde(0x40);	 // VCOMH Deselect Level

	AOLED_WriteCmde(0xB0);

	AOLED_WriteCmde(0xA4);	 // Set all pixels OFF
	AOLED_WriteCmde(0xA6);	 // Set display not inverted
	AOLED_WriteCmde(0xAF);	 // Set display On

	AOLED_FillScreen(0);
}

#define I2Cx_MSA		0x0000
#define I2Cx_MCS		0x0004
#define I2Cx_MDR		0x0008
#define I2Cx_FIFOCTL	0x0F04

#define MCS_RUN		0x01
#define MCS_START	0x02
#define MCS_STOP	0x04
#define MCS_ACK		0x08
#define MCS_BUSY	0x01
#define MCS_ERROR	0x02

static void TIVA_I2C2_SendData(char slaveAdr, short nbdata, char* pBuff) {
	unsigned int value, cmde;
	short nerr, nbtst;

	nerr = -2;
	if (nbdata < 0 || nbdata > 127)
		return;
	HWREG(I2C2_BASE + I2Cx_MSA) = slaveAdr & 0x00FE;
	nerr = -3;
	value = *pBuff++;   nbdata--;
	HWREG(I2C2_BASE + I2Cx_MDR) = value;
	cmde = MCS_RUN | MCS_START;
	while (1) {
		if (nbdata == 0)
			cmde |= MCS_STOP;
		HWREG(I2C2_BASE + I2Cx_MCS) = cmde;   nbtst = 10;
		while (nbtst > 0) {
			value = HWREG(I2C2_BASE + I2Cx_MCS);
			if ((value & MCS_BUSY) == 0)
				break;
			delayMicroseconds(20);
			nbtst--;
		}
		delayMicroseconds(100);
		if (nbdata == 0)
			break;
		value = *pBuff++;   nbdata--;
		HWREG(I2C2_BASE + I2Cx_MDR) = value;
		cmde = MCS_RUN;
	}
	nerr = 0;
}

static void TIVA_I2C2_InitModule(short i2cmun) {
	if (i2cmun != 2)
		return;

	SysCtlPeripheralEnable(SYSCTL_PERIPH_I2C2);
	//reset module
	SysCtlPeripheralReset(SYSCTL_PERIPH_I2C2);
	//enable GPIO peripheral that contains I2C 2
	SysCtlPeripheralEnable(SYSCTL_PERIPH_GPIOE);
	// Configure the pin muxing for I2C0 functions on port B2 and B3.
	GPIOPinConfigure(GPIO_PE4_I2C2SCL);
	GPIOPinConfigure(GPIO_PE5_I2C2SDA);
	// Select the I2C function for these pins.
	GPIOPinTypeI2CSCL(GPIO_PORTE_BASE, GPIO_PIN_4);
	GPIOPinTypeI2C(GPIO_PORTE_BASE, GPIO_PIN_5);

	// Enable and initialize the I2C0 master module. 
	// The last parameter sets the I2C data transfer rate.
	// false = 100kbps and true = 400kbps.
	I2CMasterInitExpClk(I2C2_BASE, SysCtlClockGet(), true);

	//clear I2C FIFOs
	HWREG(I2C2_BASE + I2Cx_FIFOCTL) = 80008000;
}
