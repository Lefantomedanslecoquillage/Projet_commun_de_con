<?php
session_start();
require_once __DIR__ . "/../app/controllers/AuthController.php";

$action = $_GET["action"] ?? "";
$controller = new AuthController();

switch ($action) {
	case "login":
		$controller->login();
		break;
	case "logout":
		$controller->logout();
		break;
	default:
		if (!isset($_SESSION["user"])) {
			$controller->homepage();
		} else {
			$controller->dashboard();
		}
}