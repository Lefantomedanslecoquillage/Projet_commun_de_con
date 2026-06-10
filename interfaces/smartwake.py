import configparser
import pathlib
import time

import pymysql
import serial


# get config file
root_path = pathlib.Path(__file__).parent.absolute()
config_path = root_path / "../config.ini"
if not pathlib.Path(config_path).is_file():
	raise FileNotFoundError(f"Le fichier de configuration '{config_path}' est introuvable.")

with open(config_path, "r") as configfile:
	config = configparser.ConfigParser()
	config.read(config_path)


# connect to serial port
serial = serial.Serial()
serial.baudrate = config.get("serial", "baudrate")
serial.port = config.get("serial", "port")

while not serial.is_open:
	serial.open()


# connect to remote database
host = config.get("database", "host")
port = config.getint("database", "port")
dbname = config.get("database", "dbname")
user = config.get("database", "username")
password = config.get("database", "password")

connection_params = {
	"host": host,
	"port": port,
	"user": user,
	"password": password,
	"database": dbname
}

connection = pymysql.connect(**connection_params)
cursor = connection.cursor()

while True:
	line = serial.readline().decode("utf-8", errors="ignore").strip()
	if not line:
		continue

	sql = "INSERT INTO CO2 (timestamp, value) VALUES (NOW(), %s)"
	# not inserted yet
	co2_value = int(line)
	cursor.execute(sql, (co2_value,))
	print(f"Valeur de CO2 insérer : {co2_value}, le {time.strftime('%Y-%m-%d %H:%M:%S')}")

serial.close()

cursor.close()
connection.close()