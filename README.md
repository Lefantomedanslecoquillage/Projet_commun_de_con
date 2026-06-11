# Prérequis

Un fichier `/app/models/config.ini` doit se trouver à la racine de ce répertoire et il ressemble à ceci :
```ini
[serial]
baudrate = 9600
port = COM?

[database]
host = ?
port = 3306
dbname = ?
username = ?
password = ?
```
Les valeurs sont disponibles ici :
[Hangar &ndash; Garage ISEP](https://hangar.garageisep.com/projects/51).

## Librairies Arduino

Installez les librairies Seeed Arduino SGP30.

## Librairies Python

Installez les libraries requises (pymysql et pyserial) avec `pip install -r interfaces/requirements.txt`

# Utilisation

1. Brancher la carte TIVA et lancer `interfaces/serial.py`
pour lire et envoyer des données vers la base de donnée externe.
2. Allez sur le site [SmartWake](https://hangar.garageisep.com/projects/51)
et connectez-vous pour voir les valeurs.

Une LED rouge statique indique que les capteurs ne sont pas encore prêts.<br>
Une LED bleue clignotante indique des données sont envoyés sur le port Serial.

L'écran se mettra en veille après une minute, vous pouvez le réveiller en appuyant sur le bouton SW1.
Cette opération sera confirmée par le clignotement de la LED verte.