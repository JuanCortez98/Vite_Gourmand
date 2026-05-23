document.addEventListener('DOMContentLoaded', () => {
    console.log("Script about.js chargé correctement");

    const elements = document.querySelectorAll('[data-aos]');
    console.log(`Éléments avec data-aos trouvés : ${elements.length}`);

    if (elements.length === 0) {
        console.warn("Aucun élément avec data-aos trouvé → rien à animer");
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                console.log(`Élément visible : ${entry.target.tagName} → classe .visible ajoutée`);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: "0px 0px -10% 0px"
    });

    elements.forEach(el => observer.observe(el));

    // Bouton retour en haut
    const scrollBtn = document.getElementById('scroll-top');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('show', window.scrollY > 300);
        });

        scrollBtn.addEventListener('click', e => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});