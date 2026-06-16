<?php
include_once "head.html";
include_once "header.php";

// Connexion BDD
$host = 'localhost';
$dbname = 'hangardb_axst62997';
$user = 'axst62997';
$password = 'vN98OBrkug96JSeUmiFxuZGp';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

// Dernière mesure météo
$stmt = $pdo->query("SELECT temperature, humidite FROM groupe_4B ORDER BY horodatage DESC LIMIT 1");
$meteo = $stmt->fetch(PDO::FETCH_ASSOC);

// Avant-dernière mesure
$stmt2 = $pdo->query("SELECT temperature, humidite FROM groupe_4B ORDER BY horodatage DESC LIMIT 1 OFFSET 1");
$meteo_precedent = $stmt2->fetch(PDO::FETCH_ASSOC);

// Variations température
$variation_temp = $meteo['temperature'] - $meteo_precedent['temperature'];
$fleche_temp = $variation_temp > 0 ? '↗️' : ($variation_temp < 0 ? '↘️' : '→');
$signe_temp = $variation_temp > 0 ? '+' : '';

// Variations humidité
$variation_hum = $meteo['humidite'] - $meteo_precedent['humidite'];
$fleche_hum = $variation_hum > 0 ? '↗️' : ($variation_hum < 0 ? '↘️' : '→');
$signe_hum = $variation_hum > 0 ? '+' : '';
?>

<main>
    <div class="ctn air-section-container">
        <h1>Température</h1>
        <p class="value"><?= $meteo['temperature'] ?> °C</p>
        <p class="evolution"><?= $fleche_temp ?> <?= $signe_temp . number_format($variation_temp, 2) ?> °C</p>
    </div>
    <div class="ctn air-section-container">
        <h1>Humidité</h1>
        <p class="value"><?= $meteo['humidite'] ?> %</p>
        <p class="evolution"><?= $fleche_hum ?> <?= $signe_hum . number_format($variation_hum, 2) ?> %</p>
    </div>
</main>