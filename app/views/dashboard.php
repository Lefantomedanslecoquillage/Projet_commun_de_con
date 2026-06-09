<?php
include_once "head.html";
include_once "header.php";
?>

<main>
	<h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']); ?> !</h1>
</main>
