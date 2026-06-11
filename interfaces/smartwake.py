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

print(r"""
     .==-_-,
    /',o :"       En attente des valeurs...
    |;:""(
   /: ,.:\        Si les valeurs ne viennent toujours pas,
  /: :;  ';       vérifiez que la LED bleue de la carte clignote.
  : ::\   (
 /::\::|  ,`.     Si la LED ne clignote pas,
/::; \;'  |\)     vérifiez que tous les câbles sont branchés.
\::   )  ,'
/:: ,~  /         Si ça ne fonctionne toujours pas, appelez le pingouin.
`' )<  )<

""")

while True:
	co2 = int(serial.readline().decode("utf-8", errors="ignore").strip())
	ch4 = int(serial.readline().decode("utf-8", errors="ignore").strip())
	voc = int(serial.readline().decode("utf-8", errors="ignore").strip())
	if not co2:
		continue

	cursor.execute("INSERT INTO CO2 (timestamp, value) VALUES (NOW(), %s)", (co2,))
	cursor.execute("INSERT INTO CH4 (timestamp, value) VALUES (NOW(), %s)", (ch4,))
	cursor.execute("INSERT INTO VOC (timestamp, value) VALUES (NOW(), %s)", (voc,))
	connection.commit()

	print(f"{time.strftime('%Y-%m-%d %H:%M:%S')}")
	print(f"Valeur de CO2 : {co2:>5} ppm")
	print(f"Valeur de CH4 : {ch4:>5} ppm")
	print(f"Valeur de VOC : {voc:>5} ppb\n")

serial.close()

cursor.close()
connection.close()