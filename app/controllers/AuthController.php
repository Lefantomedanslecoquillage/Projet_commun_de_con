<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Sensor.php";

class AuthController {
	public function dashboard($section) {
		if ($section == "air") $this->dashboardAir();
		else if ($section === "light") $this->dashboardLight();
		else if ($section === "sound") $this->dashboardSound();
		else if ($section === "environment") $this->dashboardEnvironment();
		else $this->dashboardMain();
	}

	public function dashboardMain() {
		$data = Sensor::getLastTwoData();

		$co2Values = array_column($data, "CO2");
		$ch4Values = array_column($data, "CH4");
		$vocValues = array_column($data, "VOC");

		if (isset($_GET["format"]) && $_GET["format"] === "json") {
			header("Content-Type: application/json");
			echo json_encode(["co2" => $co2Values, "ch4" => $ch4Values, "voc" => $vocValues]);
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

	public function dashboardLight() {
		require __DIR__ . "/../views/dashboardLight.php";
	}

	public function dashboardSound() {
		require __DIR__ . "/../views/dashboardSound.php";
	}

	public function dashboardEnvironment() {
		require __DIR__ . "/../views/dashboardEnvironment.php";
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