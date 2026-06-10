<?php
require_once __DIR__ . '/Database.php';

class Sensor {
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

		// Formatage {x, y}
		$co2Points = array_map(fn($r) => ['x' => $r['timestamp'], 'y' => (int)$r['value']], $co2Data);
		$vocPoints = array_map(fn($r) => ['x' => $r['timestamp'], 'y' => (int)$r['value']], $vocData);

		return [
			'datasets' => [
				[
					'label'           => 'CO2',
					'data'            => $co2Points,
					'borderColor'     => 'rgb(75, 192, 192)',
					'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
					'tension'         => 0.3
				],
				[
					'label'           => 'VOC',
					'data'            => $vocPoints,
					'borderColor'     => 'rgb(255, 99, 132)',
					'backgroundColor' => 'rgba(255, 99, 132, 0.1)',
					'tension'         => 0.3
				]
			]
		];
	}
}