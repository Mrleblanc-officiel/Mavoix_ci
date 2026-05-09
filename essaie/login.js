const btn = document.querySelector(".btnLogin");
const divUser = document.querySelector("#divUser");
const divPass = document.querySelector("#divPass");

if (true) {
	btn.addEventListener("click", () => {
		divUser.style.display = "none";
		divPass.style.display = "none";
		btn.innerHTML = "Bienvenue";

		alert("Connexion Réussie");
	});
}
