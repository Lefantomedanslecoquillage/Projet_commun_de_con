<?php
require_once __DIR__ . "/Database.php";

class User {
	public static function authenticate($username, $password) {
		$pdo = Database::getConnection();
		$stmt = $pdo->prepare("SELECT * FROM User WHERE username = :username");
		$stmt->execute(["username" => $username]);
		$user = $stmt->fetch();

		if ($user && password_verify($password, $user["password"])) {
			return $user;
		}
		return false;
	}
}