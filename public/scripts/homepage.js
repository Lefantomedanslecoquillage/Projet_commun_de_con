const homepageLoginBtn =
    document.getElementById("homepageLoginBtn")

if (homepageLoginBtn) {
    homepageLoginBtn.addEventListener("click", () => {
        document
            .getElementById("loginModal")
            .showModal?.()

        document
            .getElementById("loginModal")
            .style.display = "flex"
    })
}