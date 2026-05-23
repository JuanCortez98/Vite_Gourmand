document.addEventListener('DOMContentLoaded', () => {
    console.log("Script des menus chargé correctement");

    const menus = [
        {
            titre: "Menu Noël Festif",
            images: [],
            description: "Un menu spécial pour les fêtes de Noël, avec des saveurs traditionnelles et festives.",
            theme: "Noël",
            plats: {
                entrees: ["Soupe de potiron aux épices (allergènes : lactose)"],
                plats: ["Dinde rôtie farcie (allergènes : gluten, noix)"],
                desserts: ["Bûche de Noël au chocolat (allergènes : œufs, gluten)"]
            },
            personnesMin: 1,
            prixMin: 45.00,
            conditions: "Commander au moins 1 semaine à l'avance. Stocker au frais < 4°C.",
            regime: "classique",
            stock: 5
        },
        {
            titre: "Menu Végétarien Pâques",
            images: [],
            description: "Un menu frais et coloré pour Pâques, adapté aux végétariens.",
            theme: "Pâques",
            plats: {
                entrees: ["Salade printanière (allergènes : aucun)"],
                plats: ["Risotto aux légumes (allergènes : lactose)"],
                desserts: ["Tarte aux fruits (allergènes : gluten)"]
            },
            personnesMin: 2,
            prixMin: 25.00,
            conditions: "Commander 3 jours à l'avance. Consommer dans les 24h.",
            regime: "végétarien",
            stock: 8
        },
        {
            titre: "Menu Vegan Classique",
            images: [],
            description: "Un menu quotidien vegan, simple et nutritif.",
            theme: "classique",
            plats: {
                entrees: ["Hummus et crudités (allergènes : sésame)"],
                plats: ["Burger vegan (allergènes : gluten, soja)"],
                desserts: ["Sorbet aux fruits (allergènes : aucun)"]
            },
            personnesMin: 1,
            prixMin: 15.00,
            conditions: "Prêt en 2 heures. Pas de stockage spécifique.",
            regime: "vegan",
            stock: 10
        },
        // Ajouter plus si nécessaire
    ];

    // Fonction pour afficher les menus
    function afficherMenus(menusAAfficher) {
        const liste = document.createElement('div');
        liste.innerHTML = '';

        menusAAfficher.forEach(menu => {
            const carte = document.createElement('div');
            carte.className = 'menu-carte';

            // Galerie d'images
            const imagesDiv = document.createElement('div');
            imagesDiv.className = 'menu-images';
            menu.images.forEach(src => {
                const img = document.createElement('img');
                img.src = src;
                img.alt = menu.titre;
                imagesDiv.appendChild(img);
            });
            carte.appendChild(imagesDiv);

            // Contenu
            const contenu = document.createElement('div');
            contenu.className = 'menu-contenu';

            contenu.innerHTML = `
                <h3 class="menu-titre">${menu.titre}</h3>
                <p class="menu-desc">${menu.description}</p>
                <p class="menu-info"><strong>Thème :</strong> ${menu.theme}</p>
                <p class="menu-info"><strong>Régime :</strong> ${menu.regime}</p>
                <p class="menu-info"><strong>Personnes min :</strong> ${menu.personnesMin}</p>
                <p class="menu-info"><strong>Prix pour min (€) :</strong> ${menu.prixMin.toFixed(2)}</p>
                <p class="menu-info"><strong>Conditions :</strong> ${menu.conditions}</p>
                <p class="menu-stock">Stock disponible : ${menu.stock} commandes restantes</p>
            `;

            // Liste des plats
            const platsDiv = document.createElement('div');
            platsDiv.className = 'menu-plats';
            platsDiv.innerHTML = '<h4>Plats possibles :</h4>';

            const entreesUl = document.createElement('ul');
            menu.plats.entrees.forEach(entree => {
                const li = document.createElement('li');
                li.textContent = entree;
                entreesUl.appendChild(li);
            });
            platsDiv.appendChild(entreesUl);

            const platsUl = document.createElement('ul');
            menu.plats.plats.forEach(plat => {
                const li = document.createElement('li');
                li.textContent = plat;
                platsUl.appendChild(li);
            });
            platsDiv.appendChild(platsUl);

            const dessertsUl = document.createElement('ul');
            menu.plats.desserts.forEach(dessert => {
                const li = document.createElement('li');
                li.textContent = dessert;
                dessertsUl.appendChild(li);
            });
            platsDiv.appendChild(dessertsUl);

            contenu.appendChild(platsDiv);
            carte.appendChild(contenu);
            liste.appendChild(carte);
        });
    }

    // Appliquer les filtres
    function appliquerFiltres() {
        const prixMax = parseFloat(document.getElementById('prix-max').value) || Infinity;
        const prixMin = parseFloat(document.getElementById('prix-min').value) || 0;
        const prixMaxRange = parseFloat(document.getElementById('prix-max-range').value) || Infinity;
        const theme = document.getElementById('theme').value;
        const regime = document.getElementById('regime').value;
        const personnesMin = parseFloat(document.getElementById('personnes-min').value) || 0;

        const filtres = menus.filter(menu => {
            return menu.prixMin <= prixMax &&
                   menu.prixMin >= prixMin &&
                   menu.prixMin <= prixMaxRange &&
                   (theme === '' || menu.theme === theme) &&
                   (regime === '' || menu.regime === regime) &&
                   menu.personnesMin >= personnesMin;
        });

        afficherMenus(filtres);
    }

    // Réinitialiser les filtres
    function reinitialiserFiltres() {
        document.getElementById('filtres-form').reset();
        afficherMenus(menus);
    }

    // Déconnexion
    function deconnexion() {
        if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
            localStorage.removeItem("token");
            window.location.href = "login.html";
        }
    }

    // Initialisation
    afficherMenus(menus);
});