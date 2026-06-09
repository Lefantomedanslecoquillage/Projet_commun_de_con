<?php
class Database {
	private static $pdo = null;

	public static function getConnection() {
		if (self::$pdo === null) {
			$configPath = __DIR__ . "/config.ini";
			if (file_exists($configPath)) {
				$config = parse_ini_file($configPath, true);
				$db = $config["database"];

				$host = $db["host"];
				$port = $db["port"];
				$dbname = $db["dbname"];
				$username = $db["username"];
				$password = $db["password"];
			} else {
				$host = getenv("DB_HOST");
				$port = getenv("DB_PORT");
				$dbname = getenv("DB_NAME");
				$username = getenv("DB_USER");
				$password = getenv("DB_PASSWORD");

				if (empty($host) || empty($port) || empty($dbname) || empty($username) || empty($password)) {
					die("Configuration introuvable. Créez config.ini ou définissez les variables d'environnement");
				}
			}

			try {
				self::$pdo = new PDO(
					"mysql:host={$host};port={$port};dbname={$dbname}",
					$username,
					$password,
					[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
					]
				);
			} catch (PDOException $exc) {
				die("Erreur de connexion : " . $exc->getMessage());
			}
		}

		return self::$pdo;
	}
}