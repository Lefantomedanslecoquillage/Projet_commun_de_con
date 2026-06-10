<?php
require_once __DIR__ . "/Database.php";

class Sensor
{
	public static function getLast60MinutesData(): array
	{
		$pdo = Database::getConnection();

		// CO2
		$stmtCO2 = $pdo->prepare(
			"SELECT timestamp, value FROM CO2 WHERE timestamp >= NOW() - INTERVAL 60 MINUTE ORDER BY timestamp ASC"
		);
		$stmtCO2->execute();
		$co2Data = $stmtCO2->fetchAll();

		// VOC
		$stmtVOC = $pdo->prepare(
			"SELECT timestamp, value FROM VOC WHERE timestamp >= NOW() - INTERVAL 60 MINUTE ORDER BY timestamp ASC"
		);
		$stmtVOC->execute();
		$vocData = $stmtVOC->fetchAll();

		// Transformation en {x, y}
		$co2Points = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["value"]], $co2Data);
		$vocPoints = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["value"]], $vocData);

		return [
			"co2" => $co2Points,
			"voc" => $vocPoints
		];
	}
}