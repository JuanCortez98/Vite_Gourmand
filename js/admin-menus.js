document.addEventListener('DOMContentLoaded', function () {
  const apiUrl = '../public/api.php';
  const csrfToken = document.body.dataset.csrfToken;
  const root = document.getElementById('admin-menus-root');
  const message = document.getElementById('admin-menus-message');
  const form = document.getElementById('admin-menu-form');
  const syncButton = document.getElementById('admin-menus-sync');

  function showMessage(text, type = 'success') {
    if (!message) return;
    message.textContent = text;
    message.className = `alert alert-${type}`;
    message.style.display = 'block';
  }

  function clearMessage() {
    if (!message) return;
    message.textContent = '';
    message.style.display = 'none';
  }

  function loadMenus() {
    fetch(`${apiUrl}?resource=admin-menus`)
      .then((response) => response.json())
      .then((payload) => {
        if (!payload.ok) throw new Error(payload.message || 'Erreur API');
        renderMenus(payload.data || []);
      })
      .catch((error) => showMessage(`Impossible de charger les menus : ${error.message}`, 'danger'));
  }

  function renderMenus(menus) {
    if (!root) return;
    if (!menus.length) {
      root.innerHTML = '<p>Aucun menu pour le moment.</p>';
      return;
    }

    root.innerHTML = `
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Thème</th>
            <th>Régime</th>
            <th>Personnes</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          ${menus
            .map((menu) => `
              <tr ${menu.stock <= 5 ? 'style="background:#fee2e2;"' : ''}>
                <td>${menu.id}</td>
                <td>${escapeHtml(menu.titre)}</td>
                <td>${escapeHtml(menu.theme)}</td>
                <td>${escapeHtml(menu.regime)}</td>
                <td>${menu.personnes_minimum}</td>
                <td>${Number(menu.prix).toFixed(2)} €</td>
                <td>${menu.stock}</td>
                <td>
                  <button class="btn btn-primary btn-small" data-action="edit" data-id="${menu.id}">Modifier</button>
                  <button class="btn btn-danger btn-small" data-action="delete" data-id="${menu.id}">Supprimer</button>
                </td>
              </tr>
            `)
            .join('')}
        </tbody>
      </table>
    `;
    attachRowHandlers();
  }

  function attachRowHandlers() {
    root.querySelectorAll('button[data-action="edit"]').forEach((button) => {
      button.addEventListener('click', function () {
        const id = this.dataset.id;
        fetch(`${apiUrl}?resource=admin-menu&id=${encodeURIComponent(id)}`)
          .then((response) => response.json())
          .then((payload) => {
            if (!payload.ok) throw new Error(payload.message || 'Erreur API');
            fillForm(payload.data || {});
          })
          .catch((error) => showMessage(error.message, 'danger'));
      });
    });

    root.querySelectorAll('button[data-action="delete"]').forEach((button) => {
      button.addEventListener('click', function () {
        const id = this.dataset.id;
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce menu ?')) return;
        sendPost('admin-menu-delete', { id, csrf_token: csrfToken })
          .then((payload) => {
            if (!payload.ok) throw new Error(payload.message || 'Erreur');
            showMessage(payload.message || 'Menu supprimé.', 'success');
            loadMenus();
          })
          .catch((error) => showMessage(error.message, 'danger'));
      });
    });
  }

  function fillForm(menu) {
    if (!form) return;
    form.querySelector('[name="id"]').value = menu.id || '';
    form.querySelector('[name="titre"]').value = menu.titre || '';
    form.querySelector('[name="description"]').value = menu.description || '';
    form.querySelector('[name="theme"]').value = menu.theme || 'classique';
    form.querySelector('[name="regime"]').value = menu.regime || 'classique';
    form.querySelector('[name="personnes_minimum"]').value = menu.personnes_minimum || 2;
    form.querySelector('[name="prix"]').value = menu.prix || '';
    form.querySelector('[name="stock"]').value = menu.stock || 0;
    form.querySelector('[name="submit_button"]').textContent = menu.id ? 'Modifier' : 'Ajouter';
  }

  function sendPost(resource, body) {
    return fetch(`${apiUrl}?resource=${resource}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    }).then((res) => res.json());
  }

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  if (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      clearMessage();
      const formData = new FormData(form);
      const payload = Object.fromEntries(formData.entries());
      payload.csrf_token = csrfToken;

      sendPost('admin-menu', payload)
        .then((payload) => {
          if (!payload.ok) throw new Error(payload.message || 'Erreur');
          showMessage(payload.message || 'Menu enregistré.', 'success');
          if (form.querySelector('[name="id"]').value) {
            form.querySelector('[name="id"]').value = '';
            form.querySelector('[name="submit_button"]').textContent = 'Ajouter';
          }
          form.reset();
          loadMenus();
        })
        .catch((error) => showMessage(error.message, 'danger'));
    });
  }

  if (syncButton) {
    syncButton.addEventListener('click', function (event) {
      event.preventDefault();
      clearMessage();
      sendPost('admin-menus-sync', { csrf_token: csrfToken })
        .then((payload) => {
          if (!payload.ok) throw new Error(payload.message || 'Erreur');
          showMessage(`Synchronisation terminée : ${payload.data?.synced ?? 0} menus.`, 'success');
        })
        .catch((error) => showMessage(error.message, 'danger'));
    });
  }

  if (root) {
    loadMenus();
  }
});
