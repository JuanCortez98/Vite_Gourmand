document.addEventListener('DOMContentLoaded', () => {
    console.log("Script du panneau d'administration chargé");

    // Exemples de commandes en cours
    const commandes = [
        {
            num: "CMD-001",
            table: "Table 5",
            time: "Il y a 15 min",
            status: "en_preparation",
            items: ["Pizza margherita", "Salade César", "Eau minérale x2"]
        },
        {
            num: "CMD-002",
            table: "Table 12",
            time: "Il y a 8 min",
            status: "prete",
            items: ["Burger Classique", "Frites", "Coca-Cola"]
        },
        {
            num: "CMD-003",
            table: "À emporter",
            time: "Il y a 25 min",
            status: "en_preparation",
            items: ["Pasta Carbonara", "Tiramisu"]
        },
        {
            num: "CMD-004",
            table: "Table 3",
            time: "Il y a 5 min",
            status: "servie",
            items: ["Steak Frites", "Vin rouge", "Crème brûlée"]
        },
    ];

    // Fonction pour afficher les commandes
    function afficherCommandes(commandesAAfficher) {
        const liste = document.getElementById('commandes-liste');
        liste.innerHTML = '';

        commandesAAfficher.forEach(cmd => {
            const carte = document.createElement('div');
            carte.className = 'commande-carte';

            const enTete = document.createElement('div');
            enTete.className = 'commande-entete';

            enTete.innerHTML = `
                <span class="commande-num">#${cmd.num}</span>
                <span class="commande-statut statut-${cmd.status.replace('_', '-')}">${cmd.status.replace('_', ' ')}</span>
            `;

            const itemsUl = document.createElement('ul');
            itemsUl.className = 'commande-items';
            cmd.items.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item;
                itemsUl.appendChild(li);
            });
            carte.appendChild(itemsUl);

            const actions = document.createElement('div');
            actions.className = 'commandes-actions';
            actions.innerHTML = `
                <button class="btn btn-primary btn-small" onclick="mettreAJourStatut('${cmd.num}', 'prete')">Marquer comme prête</button>
                <button class="btn btn-outline btn-small" onclick="mettreAJourStatut('${cmd.num}', 'servie')">Marquer comme servie</button>
            `;
            carte.appendChild(actions);

            liste.appendChild(carte);
        });
    }

    // Appliquer les filtres
    function appliquerFiltres() {
        const recherche = document.getElementById('recherche-input').value.toLowerCase();
        const statut = document.getElementById('filtre-statut').value;

        const filtres = commandes.filter(cmd => {
            return (cmd.num.toLowerCase().includes(recherche) || cmd.table.toLowerCase().includes(recherche)) &&
                   (statut === '' || cmd.status === statut);
        });

        afficherCommandes(filtres);
    }

    // Déconnexion
    function deconnexion() {
        if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
            localStorage.removeItem("token");
            window.location.href = "login.html";
        }
    }

    // Mise à jour du statut (exemple, en pratique appel API)
    function mettreAJourStatut(num, nouveauStatut) {
        alert(`Commande ${num} mise à jour en "${nouveauStatut}"`);
        // Ici, appel fetch vers l'API pour mise à jour réelle
    }

    // Initialisation
    afficherCommandes(commandes);
});