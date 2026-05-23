const page = document.body.dataset.page || 'home';
const authRoot = document.getElementById('auth-root');
const appRoot = document.getElementById('app-root');
const navLinks = Array.from(document.querySelectorAll('[data-page-link]'));

function escapeHtml(value) {
  return String(value || '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
}

function renderAuth(session) {
  if (!authRoot) return;
  if (session.loggedIn) {
    authRoot.innerHTML = `<span class="welcome">Bonjour, ${escapeHtml(session.email || 'Utilisateur')} (${escapeHtml(session.role || 'client')})</span> <a href="../autentification/logout.php" class="btn btn-logout">Déconnexion</a>`;
  } else {
    authRoot.innerHTML = `<a href="../autentification/login.php" class="btn btn-login">Se connecter</a> <a href="../autentification/register.php" class="btn btn-register">S'inscrire</a>`;
  }
}

function setActiveNav() {
  navLinks.forEach((link) => {
    if (link.dataset.pageLink === page) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}

function setDocumentMeta(title, description) {
  document.title = title;
  const descElement = document.getElementById('page-description');
  const titleElement = document.getElementById('page-title');
  if (descElement) descElement.setAttribute('content', description);
  if (titleElement) titleElement.textContent = title;
}

function showMessage(message, type = 'info') {
  if (!appRoot) return;
  appRoot.innerHTML = `<div class="container"><p class="${type}">${escapeHtml(message)}</p></div>`;
}

function renderHome(data) {
  setDocumentMeta('Vite & Gourmand - Accueil', 'Traiteur familial à Bordeaux. Découvrez nos menus gourmands.');
  appRoot.innerHTML = `
    <section id="hero" class="hero d-flex align-items-center">
      <div class="container text-center">
        <h1>Vite & Gourmand</h1>
        <h2>Traiteur familial à Bordeaux depuis 25 ans</h2>
        <p>Julie & José vous proposent des menus gourmands et évolutifs pour tous vos événements.</p>
        <a href="menus.php" class="btn-get-started">Voir les menus SQL</a>
        <a href="menus-mongo.php" class="btn-get-started btn-secondary" style="margin-left:0.75rem;">Voir les menus MongoDB</a>
      </div>
    </section>
    <section id="about" class="about section">
      <div class="container">
        <div class="row gy-5 align-items-center">
          <div class="col-lg-6"></div>
          <div class="col-lg-6">
            <h3>Une histoire de gourmandise depuis 25 ans</h3>
            <p class="fst-italic lead">« Vite & Gourmand » est née il y a 25 ans à Bordeaux, de la rencontre entre Julie et José.</p>
            <ul>
              <li><i class="bi bi-check-circle-fill"></i> 25 ans d'expérience</li>
              <li><i class="bi bi-check-circle-fill"></i> Menus adaptés à chaque saison</li>
              <li><i class="bi bi-check-circle-fill"></i> Événements familiaux, Noël, Pâques et plus</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    <section id="featured" class="section light-background">
      <div class="container">
        <div class="section-title text-center">
          <h2>Menus mis en avant</h2>
        </div>
        <div id="home-featured" class="featured-grid"></div>
      </div>
    </section>
  `;

  const featuredRoot = document.getElementById('home-featured');
  if (!featuredRoot) return;
  if (!Array.isArray(data) || !data.length) {
    featuredRoot.innerHTML = '<p>Aucun menu mis en avant.</p>';
    return;
  }
  featuredRoot.innerHTML = data.map((menu) => `
    <div class="featured-item">
      <h4>${escapeHtml(menu.titre)}</h4>
      <p>${escapeHtml(menu.description)}</p>
      <p><strong>${Number(menu.prix).toFixed(2)} €</strong></p>
    </div>
  `).join('');
}

function renderStaticPage(contentHtml, title, description) {
  setDocumentMeta(title, description);
  appRoot.innerHTML = `<div class="container">${contentHtml}</div>`;
}

function renderMenusPage(menus, session) {
  setDocumentMeta('Nos Menus - Vite & Gourmand', 'Découvrez nos menus disponibles en ligne.');
  if (!Array.isArray(menus) || !menus.length) {
    appRoot.innerHTML = '<div class="container"><p>Aucun menu disponible pour le moment.</p></div>';
    return;
  }
  appRoot.innerHTML = `
    <section class="menus-hero">
      <h1>Nos Menus</h1>
      <p>Découvrez nos menus gourmands, adaptés à tous les événements et régimes.</p>
      <p class="menus-note">Vous pouvez consulter les menus depuis MySQL, MongoDB ou les deux : <a href="menus-mongo.php">Voir MongoDB</a> · <a href="menus-combined.php">Voir les deux</a></p>
    </section>
    <section class="menus-list"><div class="menus-grid" id="menus-grid"></div></section>
  `;
  const grid = document.getElementById('menus-grid');
  menus.forEach((menu) => {
    const card = document.createElement('div');
    card.className = 'menu-card';
    card.innerHTML = `
      <div class="menu-header"><h2>${escapeHtml(menu.titre)}</h2><span class="menu-theme">${escapeHtml(menu.theme)}</span></div>
      <div class="menu-info">
        <p class="menu-description">${escapeHtml(menu.description).replace(/\n/g, '<br>')}</p>
        <p><strong>Régime :</strong> ${escapeHtml(menu.regime)}</p>
        <p><strong>Pour :</strong> À partir de ${escapeHtml(menu.personnes_minimum)}</p>
        <p class="menu-price"><strong>Prix :</strong> ${Number(menu.prix).toFixed(2)} €</p>
        <p><strong>Stock restant :</strong> ${escapeHtml(menu.stock)}</p>
      </div>
    `;
    const action = document.createElement('div');
    if (session.loggedIn) {
      action.innerHTML = `<a href="../client/nouvelle-commande.php?menu_id=${encodeURIComponent(menu.id)}" class="btn btn-order">Commander</a>`;
    } else {
      action.innerHTML = '<p class="login-to-order">Connectez-vous pour commander</p>';
    }
    card.appendChild(action);
    grid.appendChild(card);
  });
}

function renderMenusMongoPage(data, session, error) {
  setDocumentMeta('Menus MongoDB - Vite & Gourmand', 'Menus chargés depuis MongoDB.');
  const items = Array.isArray(data) ? data : [];
  appRoot.innerHTML = `
    <section class="menus-hero">
      <h1>Menus MongoDB</h1>
      <p>Cette page charge les menus depuis MongoDB via un endpoint PHP.</p>
      <p class="menus-note">Si vous souhaitez comparer : <a href="menus.php">SQL</a> · <a href="menus-combined.php">SQL + MongoDB</a></p>
    </section>
    <section class="menus-list"><div id="mongo-menus-root"></div></section>
  `;
  const root = document.getElementById('mongo-menus-root');
  if (!root) return;
  if (error) {
    root.innerHTML = `<p class="error-message">${escapeHtml(error)}</p>`;
    return;
  }
  if (!items.length) {
    root.innerHTML = '<p>Aucun menu disponible depuis MongoDB pour le moment.</p>';
    return;
  }
  const grid = document.createElement('div');
  grid.className = 'menus-grid';
  items.forEach((menu) => {
    const card = document.createElement('div');
    card.className = 'menu-card';
    card.innerHTML = `
      <div class="menu-header"><h2>${escapeHtml(menu.titre || 'Sans titre')}</h2><span class="menu-theme">${escapeHtml(menu.theme || 'Thème inconnu')}</span></div>
      <div class="menu-info">
        <p class="menu-description">${escapeHtml(menu.description || '').replace(/\n/g, '<br>')}</p>
        <p><strong>Régime :</strong> ${escapeHtml(menu.regime || 'Aucun')}</p>
        <p><strong>Pour :</strong> À partir de ${escapeHtml(menu.personnes_minimum || '1')}</p>
        <p class="menu-price"><strong>Prix :</strong> ${Number(menu.prix || 0).toFixed(2)} €</p>
        <p><strong>Stock restant :</strong> ${escapeHtml(menu.stock || '0')}</p>
      </div>
    `;
    const action = document.createElement('div');
    if (session.loggedIn) {
      action.innerHTML = `<p class="login-to-order">Commande via le menu SQL uniquement.</p>`;
    } else {
      action.innerHTML = '<p class="login-to-order">Connectez-vous pour commander</p>';
    }
    card.appendChild(action);
    grid.appendChild(card);
  });
  root.appendChild(grid);
}

function renderCombinedPage(menus, session, mongoError) {
  setDocumentMeta('Menus SQL + MongoDB - Vite & Gourmand', 'Comparer les menus contenus en SQL et en MongoDB.');
  appRoot.innerHTML = `
    <section class="menus-hero"><h1>Menus SQL + MongoDB</h1><p>Comparez notre catalogue SQL avec la vue MongoDB.</p></section>
    <section class="menus-list"><h2>Menus SQL</h2><div id="combined-sql" class="menus-grid"></div></section>
    <section class="menus-list" style="margin-top:2rem;"><h2>Menus MongoDB</h2><div id="combined-mongo"></div></section>
  `;
  const sqlRoot = document.getElementById('combined-sql');
  const mongoRoot = document.getElementById('combined-mongo');
  if (!sqlRoot || !mongoRoot) return;
  if (!Array.isArray(menus) || !menus.length) {
    sqlRoot.innerHTML = '<p>Aucun menu SQL disponible.</p>';
  } else {
    menus.forEach((menu) => {
      const card = document.createElement('div');
      card.className = 'menu-card';
      card.innerHTML = `
        <div class="menu-header"><h2>${escapeHtml(menu.titre)}</h2><span class="menu-theme">${escapeHtml(menu.theme)}</span></div>
        <div class="menu-info"><p class="menu-description">${escapeHtml(menu.description).replace(/\n/g, '<br>')}</p><p><strong>Prix :</strong> ${Number(menu.prix).toFixed(2)} €</p></div>
      `;
      sqlRoot.appendChild(card);
    });
  }
  if (mongoError) {
    mongoRoot.innerHTML = `<p class="error-message">${escapeHtml(mongoError)}</p>`;
  } else {
    mongoRoot.innerHTML = '<p>Les données MongoDB ne sont pas disponibles dans cette version.</p>';
  }
}

function loadMenus(session) {
  fetch('api.php?resource=menus')
    .then((response) => response.json())
    .then((payload) => {
      if (!payload.ok) throw new Error(payload.error || 'Erreur API');
      renderMenusPage(payload.data, session);
    })
    .catch((err) => showMessage('Impossible de charger les menus : ' + err.message, 'error'));
}

function loadMenusMongo(session) {
  fetch('api.php?resource=menus-mongo')
    .then((response) => response.json())
    .then((payload) => {
      if (!payload.ok) {
        renderMenusMongoPage([], session, payload.error);
        return;
      }
      renderMenusMongoPage(payload.data, session);
    })
    .catch((err) => renderMenusMongoPage([], session, err.message));
}

function loadCombined(session) {
  fetch('api.php?resource=menus')
    .then((response) => response.json())
    .then((payload) => {
      if (!payload.ok) throw new Error(payload.error || 'Erreur API');
      renderCombinedPage(payload.data, session, 'La source MongoDB n’est pas chargée dans ce mode.');
    })
    .catch((err) => showMessage('Impossible de charger la comparaison : ' + err.message, 'error'));
}

function renderWarframesPage(data) {
  setDocumentMeta('Warframes - Vite & Gourmand', 'Explorez les maquettes de l’interface et leur metadata.');
  const items = Array.isArray(data) ? data : [];
  appRoot.innerHTML = `
    <section class="menus-hero">
      <h1>Warframes</h1>
      <p>Découvrez les designs métier et les modèles de l’application.</p>
    </section>
    <section class="menus-list">
      <div class="container">
        <div id="warframes-grid" class="menus-grid"></div>
      </div>
    </section>
  `;
  const grid = document.getElementById('warframes-grid');
  if (!grid) return;
  if (!items.length) {
    grid.innerHTML = '<p>Aucune warframe disponible pour le moment.</p>';
    return;
  }
  grid.innerHTML = items.map((item) => `
    <div class="menu-card">
      <div class="menu-header"><h2>${escapeHtml(item.name)}</h2><span class="menu-theme">${escapeHtml(item.category)}</span></div>
      <div class="menu-info">
        <p>${escapeHtml(item.description || 'Aucune description')}</p>
        <p><strong>Fichier :</strong> ${escapeHtml(item.file_path)}</p>
        <p><strong>Créé le :</strong> ${escapeHtml(item.created_at)}</p>
      </div>
    </div>
  `).join('');
}

function loadWarframesPage(session) {
  fetch('api.php?resource=warframes')
    .then((response) => response.json())
    .then((payload) => {
      if (!payload.ok) throw new Error(payload.error || 'Erreur API');
      renderWarframesPage(payload.data);
    })
    .catch((err) => showMessage('Impossible de charger les warframes : ' + err.message, 'error'));
}

function loadPage(session) {
  switch (page) {
    case 'home':
      fetch('api.php?resource=home')
        .then((response) => response.json())
        .then((payload) => {
          if (!payload.ok) throw new Error(payload.error || 'Erreur API');
          renderHome(payload.data);
        })
        .catch((err) => showMessage('Impossible de charger la page d’accueil : ' + err.message, 'error'));
      break;
    case 'menus':
      loadMenus(session);
      break;
    case 'menus-mongo':
      loadMenusMongo(session);
      break;
    case 'menus-combined':
      loadCombined(session);
      break;
    case 'about':
      renderStaticPage(`
        <section class="hero"><div class="container text-center"><h1>À propos de nous</h1><p>Votre traiteur familial à Bordeaux depuis 2001</p></div></section>
        <section class="about-section"><div class="container"><div class="row gy-5 align-items-center"><div class="col-lg-6"><img src="https://images.unsplash.com/photo-1556911220-b0b895fafb40?w=800" alt="Intérieur chaleureux" class="img-fluid"></div><div class="col-lg-6"><h3>Notre histoire</h3><p class="lead">Vite & Gourmand est né en 2001 à Bordeaux...</p></div></div></div></section>
      `, 'À propos - Vite & Gourmand', 'L’histoire et l’équipe de Vite & Gourmand.');
      break;
    case 'warframes':
      loadWarframesPage(session);
      break;
    case 'legal':
      renderStaticPage(`
        <section class="legal-main"><div class="container"><h1>Mentions Légales</h1><p>Vite & Gourmand SARL - Traiteur à Bordeaux</p><section><h2>Informations légales</h2><p>Raison sociale : Vite & Gourmand SARL</p></section></div></section>
      `, 'Mentions légales - Vite & Gourmand', 'Mentions légales du site Vite & Gourmand.');
      break;
    case 'terms':
      renderStaticPage(`
        <section class="legal-main"><div class="container"><h1>Conditions Générales de Vente</h1><p>Vite & Gourmand SARL - Traiteur à Bordeaux</p><section><h2>Article 1 - Objet</h2><p>Les présentes conditions générales de vente...</p></section></div></section>
      `, 'CGV - Vite & Gourmand', 'Conditions générales de vente du traiteur Vite & Gourmand.');
      break;
    default:
      showMessage('Page introuvable.', 'error');
  }
}

function fetchSession() {
  fetch('api.php?resource=session')
    .then((response) => response.json())
    .then((payload) => {
      if (!payload.ok) throw new Error(payload.error || 'Erreur session');
      renderAuth(payload.data || { loggedIn: false });
      setActiveNav();
      loadPage(payload.data || { loggedIn: false });
    })
    .catch((err) => {
      renderAuth({ loggedIn: false });
      setActiveNav();
      showMessage('Impossible de charger la session : ' + err.message, 'error');
    });
}

if (appRoot) {
  fetchSession();
} else {
  console.error('Element #app-root introuvable.');
}
