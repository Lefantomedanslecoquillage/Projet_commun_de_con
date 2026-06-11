<?php
require_once __DIR__ . "/Database.php";

class Sensor {
	public static function getDataByRange(int $minutes): array {
		$pdo = Database::getConnection();
		$sql = "SELECT timestamp, value FROM :table WHERE timestamp >= NOW() - INTERVAL :mins MINUTE ORDER BY timestamp ASC";

		// CO2
		$stmtCO2 = $pdo->prepare(str_replace(":table", "CO2", $sql));
		$stmtCO2->bindValue(":mins", $minutes, PDO::PARAM_INT);
		$stmtCO2->execute();
		$co2Data = $stmtCO2->fetchAll();

		// CH4
		$stmtCH4 = $pdo->prepare(str_replace(":table", "CH4", $sql));
		$stmtCH4->bindValue(":mins", $minutes, PDO::PARAM_INT);
		$stmtCH4->execute();
		$ch4Data = $stmtCH4->fetchAll();

		// VOC
		$stmtVOC = $pdo->prepare(str_replace(":table", "VOC", $sql));
		$stmtVOC->bindValue(":mins", $minutes, PDO::PARAM_INT);
		$stmtVOC->execute();
		$vocData = $stmtVOC->fetchAll();

		$co2Points = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["value"]], $co2Data);
		$ch4Points = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["value"]], $ch4Data);
		$vocPoints = array_map(fn($r) => ["x" => $r["timestamp"], "y" => (int)$r["value"]], $vocData);

		return ["co2" => $co2Points, "ch4" => $ch4Points, "voc" => $vocPoints];
	}
}