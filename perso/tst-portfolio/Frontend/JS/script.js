
// MENU HAMBURGER

const boutonMenu = document.getElementById("menu-toggle");
const menu = document.getElementById("menu");

if (boutonMenu && menu) {

    boutonMenu.addEventListener("click", function () {

        menu.classList.toggle("ouvert");

        if (menu.classList.contains("ouvert")) {

            boutonMenu.textContent = "✕";
            boutonMenu.setAttribute("aria-expanded", "true");
            boutonMenu.setAttribute("aria-label", "Fermer le menu");

        } else {

            boutonMenu.textContent = "☰";
            boutonMenu.setAttribute("aria-expanded", "false");
            boutonMenu.setAttribute("aria-label", "Ouvrir le menu");

        }

    });

    // FERMER LE MENU EN CLIQUANT SUR UN LIEN

    const liensMenu = document.querySelectorAll(".menu a");

    liensMenu.forEach(function (lien) {

        lien.addEventListener("click", function () {

            menu.classList.remove("ouvert");

            boutonMenu.textContent = "☰";
            boutonMenu.setAttribute("aria-expanded", "false");
            boutonMenu.setAttribute("aria-label", "Ouvrir le menu");

        });

    });

}


/* FORMULAIRE DE CONTACT */

// RECUPERATION DU FORMULAIRE

const formulaire = document.getElementById("form-contact");

// VERIFIER SI LE FORMULAIRE EXISTE SUR LA PAGE

if (formulaire) {

    formulaire.addEventListener("submit", async function (event) {

        // EMPECHER LE RECHARGEMENT DE LA PAGE

        event.preventDefault();

        // RECUPERATION DES CHAMPS

        const nom = document.getElementById("nom").value.trim();
        const email = document.getElementById("email").value.trim();
        const sujet = document.getElementById("sujet").value;
        const message = document.getElementById("message").value.trim();

        const resultat = document.getElementById("message-formulaire");
        const bouton = formulaire.querySelector('button[type="submit"]');

        // VERIFICATION DES CHAMPS

        if (nom === "" || email === "" || sujet === "" || message === "") {

            resultat.textContent = "Veuillez remplir tous les champs.";
            resultat.style.color = "#ff304f";
            return;

        }

        // VERIFICATION EMAIL

        if (!document.getElementById("email").checkValidity()) {

            resultat.textContent = "Adresse email invalide.";
            resultat.style.color = "#ff304f";
            return;

        }

        // PREPARATION DES DONNEES

        const donnees = new FormData(formulaire);

        // DESACTIVER LE BOUTON PENDANT L'ENVOI

        bouton.disabled = true;
        bouton.textContent = "Envoi en cours...";

        resultat.textContent = "Envoi du message...";
        resultat.style.color = "#1677ff";

        // ENVOI AU SERVEUR PHP

        try {

            const reponse = await fetch("traitement_contact.php", {
                method: "POST",
                body: donnees
            });

            const donneesPHP = await reponse.json();

            if (reponse.ok && donneesPHP.succes) {

                resultat.textContent = donneesPHP.message;
                resultat.style.color = "#40d982";

                formulaire.reset();

            } else {

                resultat.textContent = donneesPHP.message || "Erreur lors de l'envoi.";
                resultat.style.color = "#ff304f";

            }

        } catch (erreur) {

            resultat.textContent = "Impossible de contacter le serveur.";
            resultat.style.color = "#ff304f";

            console.error(erreur);

        } finally {

            bouton.disabled = false;
            bouton.textContent = "Envoyer le message";

        }

    });

}
