const menusContainer = document.getElementById('menus-container');
const errorElement = document.getElementById('menus-error');
const isLoggedIn = document.body.dataset.loggedIn === 'true';

function createMenuCard(menu) {
    const card = document.createElement('div');
    card.className = 'menu-card';

    const header = document.createElement('div');
    header.className = 'menu-header';
    header.innerHTML = `<h2>${menu.titre || 'Menu sans titre'}</h2><span class="menu-theme">${menu.theme || 'Thème inconnu'}</span>`;
    card.appendChild(header);

    const info = document.createElement('div');
    info.className = 'menu-info';
    info.innerHTML = `
        <p class="menu-description">${(menu.description || '').replace(/\n/g, '<br>')}</p>
        <p><strong>Régime :</strong> ${menu.regime || 'Aucun'}</p>
        <p><strong>Pour :</strong> À partir de ${menu.personnes_minimum || '1'} personnes</p>
        <p class="menu-price"><strong>Prix :</strong> ${parseFloat(menu.prix || 0).toFixed(2)} €</p>
        <p><strong>Stock restant :</strong> ${menu.stock ?? 0}</p>
    `;
    card.appendChild(info);

    if (isLoggedIn) {
        const button = document.createElement('a');
        button.href = `../client/nouvelle-commande.php?menu_id=${encodeURIComponent(menu._id?.$oid || menu._id || '')}`;
        button.className = 'btn btn-order';
        button.textContent = 'Commander';
        card.appendChild(button);
    } else {
        const loginText = document.createElement('p');
        loginText.className = 'login-to-order';
        loginText.textContent = 'Connectez-vous pour commander';
        card.appendChild(loginText);
    }

    return card;
}

function loadMenus() {
    fetch('menus-mongo-api.php')
        .then((response) => response.json())
        .then((payload) => {
            if (!payload.success) {
                throw new Error(payload.error || 'Erreur inconnue');
            }

            if (!payload.data.length) {
                menusContainer.innerHTML = '<p class="no-menus">Aucun menu disponible dans MongoDB pour le moment.</p>';
                return;
            }

            const grid = document.createElement('div');
            grid.className = 'menus-grid';

            payload.data.forEach((menu) => {
                grid.appendChild(createMenuCard(menu));
            });

            menusContainer.innerHTML = '';
            menusContainer.appendChild(grid);
        })
        .catch((error) => {
            errorElement.textContent = 'Impossible de charger les menus MongoDB. ' + error.message;
            errorElement.style.display = 'block';
        });
}

loadMenus();
