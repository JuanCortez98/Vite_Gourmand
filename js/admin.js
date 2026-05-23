document.addEventListener('DOMContentLoaded', () => {
    console.log("Script du panneau d'administration chargé");

    const token = localStorage.getItem('token');
    if (!token) {
        console.warn("Aucun token trouvé → redirection vers la connexion");
        window.location.href = 'login.html';
        return;
    }

    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        const role = payload.role || payload.rol || 'client';

        if (!['admin', 'administrateur'].includes(role.toLowerCase())) {
            console.warn("Rôle non autorisé pour l'administration → redirection en cours");
            window.location.href = 'login.html';
        }
    } catch (e) {
        console.error("Token invalide");
        localStorage.removeItem('token');
        window.location.href = 'login.html';
    }

    /* Défilement fluide */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const target = document.getElementById(targetId);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});

/* Bouton retour en haut */
const scrollTopBtn = document.createElement('a');
scrollTopBtn.href = '#';
scrollTopBtn.id = 'scroll-top';
scrollTopBtn.innerHTML = '<i class="bi bi-arrow-up-short"></i>';
document.body.appendChild(scrollTopBtn);

window.addEventListener("scroll", () => {
    scrollTopBtn.classList.toggle('show', window.scrollY > 300);
});

scrollTopBtn.addEventListener('click', e => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* Fonction de déconnexion */
function deconnexion() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        localStorage.removeItem('token');
        window.location.href = 'login.html';
    }
}