document.addEventListener('DOMContentLoaded', function () {
  const apiUrl = '../public/api.php';
  const csrfToken = document.body.dataset.csrfToken;
  const usersRoot = document.getElementById('admin-users-root');
  const form = document.getElementById('admin-user-form');
  const message = document.getElementById('admin-users-message');

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

  function fetchUsers() {
    fetch(`${apiUrl}?resource=admin-users`)
      .then((response) => response.json())
      .then((payload) => {
        if (!payload.ok) throw new Error(payload.message || 'Erreur API');
        renderUsers(payload.data || []);
      })
      .catch((error) => {
        showMessage(`Impossible de charger les utilisateurs : ${error.message}`, 'danger');
      });
  }

  function renderUsers(users) {
    if (!usersRoot) return;
    if (!users.length) {
      usersRoot.innerHTML = '<p>Aucun utilisateur trouvé.</p>';
      return;
    }

    usersRoot.innerHTML = `
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Inscrit le</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          ${users
            .map((user) => `
              <tr>
                <td>${user.id}</td>
                <td>${escapeHtml(user.email)}</td>
                <td>${escapeHtml(capitalize(user.role))}</td>
                <td>${formatDate(user.created_at)}</td>
                <td>${user.id === Number(document.body.dataset.currentUserId)
                  ? '<span class="text-muted">(Vous-même)</span>'
                  : `<button class="btn btn-danger btn-small" data-action="delete" data-id="${user.id}">Supprimer</button>`}
                </td>
              </tr>
            `)
            .join('')}
        </tbody>
      </table>
    `;
    attachDeleteHandlers();
  }

  function attachDeleteHandlers() {
    usersRoot.querySelectorAll('button[data-action="delete"]').forEach((button) => {
      button.addEventListener('click', function () {
        const id = this.dataset.id;
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return;
        sendPost('admin-user-delete', { id, csrf_token: csrfToken })
          .then((payload) => {
            if (!payload.ok) throw new Error(payload.message || 'Erreur');
            showMessage(payload.message || 'Utilisateur supprimé.', 'success');
            fetchUsers();
          })
          .catch((error) => showMessage(error.message, 'danger'));
      });
    });
  }

  function sendPost(resource, body) {
    return fetch(`${apiUrl}?resource=${resource}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    }).then((res) => res.json());
  }

  function capitalize(text) {
    return String(text || '').charAt(0).toUpperCase() + String(text || '').slice(1);
  }

  function formatDate(value) {
    const date = new Date(value);
    return isNaN(date.getTime()) ? '' : date.toLocaleString('fr-FR');
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

      sendPost('admin-user', payload)
        .then((payload) => {
          if (!payload.ok) throw new Error(payload.message || 'Erreur');
          showMessage(payload.message || 'Utilisateur ajouté.', 'success');
          form.reset();
          fetchUsers();
        })
        .catch((error) => showMessage(error.message, 'danger'));
    });
  }

  if (usersRoot) {
    fetchUsers();
  }
});
