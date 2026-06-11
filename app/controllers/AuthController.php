<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Sensor.php";

class AuthController {
	public function dashboard() {
		$range = isset($_GET["range"]) ? (int)$_GET["range"] : 15;
		$chartData = Sensor::getDataByRange($range);

		if (isset($_GET['format']) && $_GET['format'] === 'json') {
			header('Content-Type: application/json');
			echo json_encode($chartData);
			exit;
		}

		require __DIR__ . "/../views/dashboard.php";
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