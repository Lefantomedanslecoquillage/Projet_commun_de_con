<?php include_once "head.html"; ?>

<main>
	<h1>Connexion</h1>

	<?php if (!empty($error)): ?>
		<p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
	<?php endif; ?>

	<form method="post" action="index.php?action=login">
		<label for="username">Nom d'utilisateur :</label><br>
		<input type="text" name="username" id="username" required><br><br>

		<label for="password">Mot de passe :</label><br>
		<input type="password" name="password" id="password" required><br><br>

		<button type="submit">Se connecter</button>
	</form>
</main>
