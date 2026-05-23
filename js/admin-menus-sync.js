document.addEventListener('DOMContentLoaded', function () {
  const apiUrl = '../public/api.php';
  const root = document.getElementById('menus-sync-root');
  const csrf = document.body.dataset.csrfToken;

  function fetchStatus() {
    fetch(`${apiUrl}?resource=admin-menus-sync-status`)
      .then((r) => r.json())
      .then((payload) => {
        if (!payload.ok) throw new Error(payload.message || 'Erreur');
        renderTable(payload.data || []);
      })
      .catch((e) => {
        if (root) root.innerHTML = '<p>Impossible de charger les statuts.</p>';
      });
  }

  function renderTable(rows) {
    if (!root) return;
    if (!rows.length) {
      root.innerHTML = '<p>Aucun menu trouvé.</p>';
      return;
    }
    root.innerHTML = `
      <table class="admin-table" style="width:100%;">
        <thead><tr><th>ID</th><th>Titre</th><th>Existe en Mongo</th><th>Mongo updated_at</th><th>Action</th></tr></thead>
        <tbody>
          ${rows
            .map(
              (r) => `
            <tr>
              <td>${r.id}</td>
              <td>${escapeHtml(r.titre)}</td>
              <td>${r.mongo_exists ? 'Oui' : 'Non'}</td>
              <td>${r.mongo_updated || ''}</td>
              <td>${r.mongo_exists ? '' : `<button class="btn btn-primary btn-sync" data-id="${r.id}">Synchroniser</button>`}</td>
            </tr>
          `
            )
            .join('')}
        </tbody>
      </table>
    `;

    root.querySelectorAll('.btn-sync').forEach((b) => b.addEventListener('click', function () {
      const id = this.dataset.id;
      this.disabled = true;
      fetch(`${apiUrl}?resource=admin-menu-sync-one`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: Number(id), csrf_token: csrf }),
      })
        .then((r) => r.json())
        .then((payload) => {
          if (!payload.ok) throw new Error(payload.message || 'Erreur');
          fetchStatus();
        })
        .catch((e) => {
          alert(e.message || 'Erreur');
          this.disabled = false;
        });
    }));
  }

  function escapeHtml(s) {
    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  fetchStatus();
});
