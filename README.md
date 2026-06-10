# Prérequis

Un fichier `/app/models/config.ini` doit se trouver à la racine de ce répertoire et il ressemble à ceci :
```ini
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

Installez les librairies 

## Librairies Python

Installez les libraries requises (pymysql et pyserial) avec `pip install -r interfaces/requirements.txt`

# Utilisation

1. Brancher la carte TIVA et lancer `interfaces/serial.py`
pour lire et envoyer des données vers la base de donnée externe.
2. Allez sur le site [SmartWake](https://hangar.garageisep.com/projects/51)
et connectez-vous pour voir les valeurs.