<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Sensor.php";
require_once __DIR__ . "/../models/Database.php";

class AuthController {
	public function dashboard($section) {
		if ($section == "air") $this->dashboardAir();
		else if ($section === "environment") $this->dashboardEnvironment();
		else $this->dashboardMain();
	}

	public function dashboardMain() {
		$airData = array_reverse(Sensor::getLastTwoData("ambient_air", "timestamp"));
		$weatherData = array_reverse(Sensor::getLastTwoData("groupe_4B", "horodatage"));
		$pdo = Database::getConnection();

		// Air
		$tsValues = array_column($airData, "timestamp");
		$co2Values = array_column($airData, "CO2");
		$ch4Values = array_column($airData, "CH4");
		$vocValues = array_column($airData, "VOC");

		// Luminosité
		$lightStmt = $pdo->query("SELECT light_value FROM light_sensor_data ORDER BY created_at DESC LIMIT 2");
		$lightData = array_reverse($lightStmt->fetchAll(PDO::FETCH_COLUMN));
		$lightStmt = $pdo->query("SELECT day_status FROM light_sensor_data ORDER BY created_at DESC LIMIT 1");
		$lightStatus = $lightStmt->fetchColumn();
		if ($lightStatus === false) {
			$lightStatus = "";
		}

		// Son
		$soundStmt = $pdo->query(" SELECT raw FROM sound_sleep ORDER BY timestamp DESC LIMIT 2");
		$soundData = array_reverse( $soundStmt->fetchAll(PDO::FETCH_COLUMN) );
		$soundStmt = $pdo->query("SELECT detailed_type FROM sound_sleep ORDER BY timestamp DESC LIMIT 1");
		$soundStatus = $soundStmt->fetchColumn();
		if ($soundStatus === false) {
			$soundStatus = "";
		} else if ($soundStatus == "apnea_suspected") {
			$soundStatus = "Potentiel apnée";
		}

		if (isset($_GET["format"]) && $_GET["format"] === "json") {
			header("Content-Type: application/json");
			echo json_encode([
				"ts" => $tsValues,
				"co2" => $co2Values,
				"ch4" => $ch4Values,
				"voc" => $vocValues,

				"light" => $lightData,
				"sound" => $soundData,

				"weather" => $weatherData
			], JSON_UNESCAPED_UNICODE);
			exit;
		}

		require __DIR__ . "/../views/dashboard.php";
	}

	public function dashboardAir() {
		$range = isset($_GET["range"]) ? (int)$_GET["range"] : 15;
		$chartData = Sensor::getDataByRange($range);

		if (isset($_GET["format"]) && $_GET["format"] === "json") {
			header("Content-Type: application/json");
			echo json_encode($chartData);
			exit;
		}

		require __DIR__ . "/../views/dashboardAir.php";
	}

	public function dashboardEnvironment() {
		$range = isset($_GET["range"])
			? (int)$_GET["range"]
			: 15;

		$weatherData = Sensor::getWeatherDataByRange($range);

		$chartData = [
			"light" => Sensor::getLightDataByRange($range),
			"sound" => Sensor::getSoundDataByRange($range),
			"temperature" => $weatherData["temperature"],
			"humidity" => $weatherData["humidity"]
		];

		if (isset($_GET["format"]) && $_GET["format"] === "json") {
			header("Content-Type: application/json");
			echo json_encode($chartData);
			exit;
		}

		require __DIR__ . "/../views/dashboardEnv.php";
	}

	public function homepage() {
		require __DIR__ . "/../views/homepage.php";
	}

	public function login() {
		if (isset($_SESSION["user"])) {
			header("Location: index.php?action=dashboard");
			exit;
		}

		$error = "";

		if ($_SERVER["REQUEST_METHOD"] === "POST") {
			$username = trim($_POST["username"] ?? "");
			$password = $_POST["password"] ?? "";

			if (empty($username) || empty($password)) {
				$error = "Veuillez remplir tous les champs.";
			} else {
				$user = User::authenticate($username, $password);
				if ($user) {
					$_SESSION["user"] =  $user["username"];
					header("Location: index.php?action=dashboard");
					exit;
				} else {
					$error = "Identifiants incorrects.";
				}
			}
		}

		require __DIR__ . "/../views/homepage.php";
	}

	public function logout() {
		session_destroy();
		header("Location: index.php");
		exit;
	}
}