<link rel="stylesheet" href="/styles/header.css">
<script defer src="/scripts/header.js"></script>

<header>
	<a class="home-btn" href="index.php">SmartWake</a>
	<?php if (isset($_SESSION["user"])): ?>
		<a class="icon-btn" href="index.php?action=logout">
			<img class="icon" src="/images/logout.svg" alt="">
			<span>Se déconnecter</span>
		</a>
	<?php else: ?>
		<a id="loginButton" class="icon-btn">
			<img class="icon" src="/images/login.svg" alt="">
			<span>Se connecter</span>
		</a>
	<?php endif; ?>
</header>

<div id="loginModal" class="hide">
	<?php if (!empty($error)): ?>
		<p class="error"><?php echo htmlspecialchars($error); ?></p>
	<?php endif; ?>

	<form method="post" action="index.php?action=login">
		<label>Nom d'utilisateur :
			<input type="text" name="username" required>
		</label>
		<label>Mot de passe :
			<input type="password" name="password" required>
		</label>
		<button type="submit">Se connecter</button>
		<button type="button" id="loginCancelButton" class="danger">Annuler</button>
	</form>
</div>