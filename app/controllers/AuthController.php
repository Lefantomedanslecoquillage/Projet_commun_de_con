<?php
session_start();
require_once __DIR__ . "/../models/User.php";

class AuthController {
	public function dashboard() {
		if (!isset($_SESSION["user"])) {
			header("Location: index.php");
			exit;
		}
		require __DIR__ . "/../views/dashboard.php";
	}

	public function homepage() {
		$file = isset($_SESSION["user"]) ? __DIR__ . "/../views/dashboard.php" : __DIR__ . '/../views/homepage.php';
		require $file;
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