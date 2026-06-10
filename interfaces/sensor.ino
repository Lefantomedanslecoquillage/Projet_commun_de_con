#include "inc/hw_memmap.h"
#include "inc/hw_types.h"

#include "driverlib/i2c.h"
#include "driverlib/gpio.h"
#include "driverlib/pin_map.h"
#include "driverlib/sysctl.h"

#define I2C_NUM 1
#define SGP30_ADDR 0x58

#define I2Cx_MSA	0x0000
#define I2Cx_MCS	0x0004
#define I2Cx_MDR	0x0008

#define MCS_RUN		0x01
#define MCS_START	0x02
#define MCS_STOP	0x04
#define MCS_ACK		0x08
#define MCS_BUSY	0x01
#define MCS_ERROR	0x02

static void TIVA_I2C1_InitModule(short i2cnum) {
	if (i2cnum != 1) return;

	SysCtlPeripheralEnable(SYSCTL_PERIPH_I2C1);
	SysCtlPeripheralReset(SYSCTL_PERIPH_I2C1);

	SysCtlPeripheralEnable(SYSCTL_PERIPH_GPIOA);
	GPIOPinConfigure(GPIO_PA6_I2C1SCL);
	GPIOPinConfigure(GPIO_PA7_I2C1SDA);
	GPIOPinTypeI2CSCL(GPIO_PORTA_BASE, GPIO_PIN_6);
	GPIOPinTypeI2C(GPIO_PORTA_BASE, GPIO_PIN_7);
	// Enable internal weak pull-ups on PA6 and PA7
	I2CMasterInitExpClk(I2C1_BASE, SysCtlClockGet(), true);
}

static int TIVA_I2C1_SendData(char slaveAdr, short nbdata, char* pBuff) {
	unsigned int value, cmde;
	int nbtst;

	if (nbdata <= 0)
		return -1;

	// Adresse 7 bits -> on place l'adresse décalée (bit 0 = 0 pour écriture)
	HWREG(I2C1_BASE + I2Cx_MSA) = (slaveAdr << 1) & 0xFE;

	// Premier octet
	value = *pBuff++;
	nbdata--;
	HWREG(I2C1_BASE + I2Cx_MDR) = value;
	cmde = MCS_RUN | MCS_START;

	while (1) {
		if (nbdata == 0)
			cmde |= MCS_STOP;

		HWREG(I2C1_BASE + I2Cx_MCS) = cmde;
		nbtst = 1000;   // timeout
		while (nbtst > 0) {
			value = HWREG(I2C1_BASE + I2Cx_MCS);
			if (!(value & MCS_BUSY))
				break;
			delayMicroseconds(10);
			nbtst--;
		}
		if (nbtst == 0)
			return -1;  // timeout

		if (value & MCS_ERROR)
			return -1;  // erreur bus

		if (nbdata == 0)
			break;

		value = *pBuff++;
		nbdata--;
		HWREG(I2C1_BASE + I2Cx_MDR) = value;
		cmde = MCS_RUN;
	}
	return 0;
}

static int TIVA_I2C1_ReadData(char slaveAdr, short nbdata, char* pBuff) {
	uint32_t value, cmde;
	int nbtst;

	if (nbdata <= 0)
		return -1;

	// Set address + read bit
	HWREG(I2C1_BASE + I2Cx_MSA) = ((slaveAdr << 1) & 0xFE) | 1;

	// First command: START + (STOP if single byte) + (ACK if more than 1)
	cmde = MCS_RUN | MCS_START;
	if (nbdata == 1)
		cmde |= MCS_STOP;
	else if (nbdata > 1)
		cmde |= MCS_ACK;

	while (1) {
		HWREG(I2C1_BASE + I2Cx_MCS) = cmde;

		nbtst = 1000;
		while (nbtst > 0) {
			value = HWREG(I2C1_BASE + I2Cx_MCS);
			if (!(value & MCS_BUSY))
				break;
			delayMicroseconds(10);
			nbtst--;
		}
		if (nbtst == 0)
			return -1;
		if (value & MCS_ERROR)
			return -1;

		*pBuff++ = HWREG(I2C1_BASE + I2Cx_MDR) & 0xFF;
		nbdata--;

		if (nbdata == 0)
			break;

		// Prepare for next byte
		if (nbdata == 1)
			cmde = MCS_RUN | MCS_STOP;	// last byte: STOP, no ACK
		else
			cmde = MCS_RUN | MCS_ACK;	 // more bytes: continue with ACK
	}
	return 0;
}

void SGP30_Init(void) {
	TIVA_I2C1_InitModule(I2C_NUM);
	delay(2);
	char initCmd[2] = {0x20, 0x03};
	if (TIVA_I2C1_SendData(SGP30_ADDR, 2, initCmd) != 0) {
		while(1);   // hang
	}
}

static char SGP30_GenerateCRC(char* data, int count) {
	unsigned char crc = 0xFF;
	for (int i = 0; i < count; i++) {
		crc ^= (unsigned char)data[i];
		for (int bit = 0; bit < 8; bit++) {
			crc = (crc & 0x80) ? (crc << 1) ^ 0x31 : (crc << 1);
		}
	}
	return (char)crc;
}

int SGP30_ReadMeasurement(unsigned short* co2_ppm, unsigned short* tvoc_ppb) {
	char cmd[2] = {0x20, 0x08};
	char raw[6];
	int res;

	res = TIVA_I2C1_SendData(SGP30_ADDR, 2, cmd);
	if (res != 0) return -1;

	delay(10);

	res = TIVA_I2C1_ReadData(SGP30_ADDR, 6, raw);
	if (res != 0) return -2;

	// Validate CRCs
	if (SGP30_GenerateCRC(raw, 2) != raw[2]) return -3;
	if (SGP30_GenerateCRC(raw + 3, 2) != raw[5]) return -4;

	*co2_ppm = ((unsigned char)raw[0] << 8) | (unsigned char)raw[1];
	*tvoc_ppb = ((unsigned char)raw[3] << 8) | (unsigned char)raw[4];
	return 0;
}
