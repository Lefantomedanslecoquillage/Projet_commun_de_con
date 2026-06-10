const loginButton = document.getElementById("loginButton");

if (loginButton) {
	document.getElementById("loginButton").addEventListener("click", function () {
		document.getElementById("loginModal").style.display = "flex"
	})

	document.getElementById("loginCancelButton").addEventListener("click", function () {
		document.getElementById("loginModal").style.display = "none"
	})
}
