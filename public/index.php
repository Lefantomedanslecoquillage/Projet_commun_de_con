<?php
require_once __DIR__ . "/../app/controllers/AuthController.php";

$action = $_GET["action"] ?? "";
$controller = new AuthController();

switch ($action) {
	case "login":
		$controller->login();
		break;
	case "dashboard":
		$controller->dashboard();
		break;
	case "logout":
		$controller->logout();
		break;
	default:
		$controller->homepage();
}