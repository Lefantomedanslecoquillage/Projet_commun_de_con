<?php
require_once __DIR__ . "/Database.php";

class Sensor {
	public static function getDataByRange(int $minutes): array {
		$pdo = Database::getConnection();
		$sql = "SELECT * FROM ambient_air WHERE timestamp >= NOW() - INTERVAL :mins MINUTE ORDER BY timestamp ASC";

		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(":mins", $minutes, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetchAll();

		$co2Points = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["CO2"]], $data);
		$ch4Points = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["CH4"]], $data);
		$vocPoints = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["VOC"]], $data);

		return ["co2" => $co2Points, "ch4" => $ch4Points, "voc" => $vocPoints];
	}

	public static function getLastTwoData(): array {
		$pdo = Database::getConnection();
		$stmt = $pdo->query("SELECT * FROM ambient_air ORDER BY timestamp DESC LIMIT 2");
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
}