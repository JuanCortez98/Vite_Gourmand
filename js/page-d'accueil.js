document.addEventListener('DOMContentLoaded', function () {
    console.log("page-d'accueil.js → chargé correctement");

    // Défilement fluide vers les sections
    const liensScroll = document.querySelectorAll('a.scrollto');
    console.log(`Liens .scrollto trouvés : ${liensScroll.length}`);

    liensScroll.forEach(lien => {
        lien.addEventListener('click', function (e) {
            e.preventDefault();

            const idCible = this.getAttribute('href').substring(1);
            const elementCible = document.getElementById(idCible);

            if (elementCible) {
                const hauteurHeader = document.querySelector('.header')?.offsetHeight || 0;
                const positionY = elementCible.getBoundingClientRect().top + window.pageYOffset - hauteurHeader;

                window.scrollTo({
                    top: positionY,
                    behavior: 'smooth'
                });
            } else {
                console.warn(`Aucun élément trouvé avec l'ID : #${idCible}`);
            }
        });
    });

    // Bouton retour en haut
    const boutonRetourHaut = document.createElement('a');
    boutonRetourHaut.href = '#';
    boutonRetourHaut.id = 'scroll-top';
    boutonRetourHaut.innerHTML = '<i class="bi bi-arrow-up-short"></i>';
    boutonRetourHaut.className = 'scroll-top';
    document.body.appendChild(boutonRetourHaut);

    window.addEventListener('scroll', () => {
        boutonRetourHaut.classList.toggle('show', window.scrollY > 300);
    });

    boutonRetourHaut.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Apparition progressive (les textes apparaissent en descendant)
    console.log("Initialisation de l'IntersectionObserver pour les animations...");

    const elementsAnimes = document.querySelectorAll('.section, .team-member');

    console.log(`Éléments animés trouvés : ${elementsAnimes.length}`);
    if (elementsAnimes.length === 0) {
        console.warn("Aucun élément .section ni .team-member trouvé → aucune animation");
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    console.log(`Animation activée sur : ${entry.target.tagName} → classe .visible ajoutée`);
                }
            });
        },
        {
            threshold: 0.12,          
            rootMargin: '0px 0px -15% 0px' 
        }
    );

    elementsAnimes.forEach(el => {
        observer.observe(el);
    });
});