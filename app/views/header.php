<link rel="stylesheet" href="/styles/header.css">

<header>
	<a class="home-btn" href="index.php">SmartWake</a>
	<?php if (isset($_SESSION["user"])): ?>
		<a class="icon-btn" href="index.php?action=logout">
			<img class="icon" src="/images/logout.svg" alt="">
			<span>Se déconnecter</span>
		</a>
	<?php else: ?>
		<a class="icon-btn" href="index.php?action=login">
			<img class="icon" src="/images/login.svg" alt="">
			<span>Se connecter</span>
		</a>
	<?php endif; ?>
</header>
