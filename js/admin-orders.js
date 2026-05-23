document.addEventListener('DOMContentLoaded', function () {
  const apiUrl = '../public/api.php';
  const csrfToken = document.body.dataset.csrfToken;
  const root = document.getElementById('admin-orders-root');
  const message = document.getElementById('admin-orders-message');

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

  function loadOrders() {
    fetch(`${apiUrl}?resource=admin-orders`)
      .then((response) => response.json())
      .then((payload) => {
        if (!payload.ok) throw new Error(payload.message || 'Erreur API');
        renderOrders(payload.data || []);
      })
      .catch((error) => showMessage(`Impossible de charger les commandes : ${error.message}`, 'danger'));
  }

  function renderOrders(orders) {
    if (!root) return;
    if (!orders.length) {
      root.innerHTML = '<p>Aucune commande pour le moment.</p>';
      return;
    }

    root.innerHTML = `
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Date</th>
            <th>Total</th>
            <th>État</th>
            <th>Adresse</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          ${orders
            .map((order) => `
              <tr>
                <td>${order.id}</td>
                <td>${escapeHtml(order.client_email || 'Client inconnu')}</td>
                <td>${formatDate(order.created_at)}</td>
                <td>${Number(order.total).toFixed(2)} €</td>
                <td>${renderStatusSelect(order)}</td>
                <td>${escapeHtml(order.adresse_livraison || 'Non spécifiée')}</td>
                <td>
                  <button class="btn btn-danger btn-small" data-action="delete" data-id="${order.id}">Supprimer</button>
                </td>
              </tr>
            `)
            .join('')}
        </tbody>
      </table>
    `;
    attachHandlers();
  }

  function renderStatusSelect(order) {
    return `
      <form class="status-form" data-id="${order.id}">
        <select name="status" class="status-select">
          ${['en_cours', 'prete', 'servie', 'annulee']
            .map((status) => `<option value="${status}" ${order.status === status ? 'selected' : ''}>${capitalize(status)}</option>`)
            .join('')}
        </select>
      </form>
    `;
  }

  function attachHandlers() {
    root.querySelectorAll('.status-form').forEach((form) => {
      form.addEventListener('change', function (event) {
        event.preventDefault();
        const id = this.dataset.id;
        const status = this.querySelector('[name="status"]').value;
        clearMessage();
        sendPost('admin-order', { id, status, csrf_token: csrfToken })
          .then((payload) => {
            if (!payload.ok) throw new Error(payload.message || 'Erreur');
            showMessage(payload.message || 'État mis à jour.', 'success');
            loadOrders();
          })
          .catch((error) => showMessage(error.message, 'danger'));
      });
    });

    root.querySelectorAll('button[data-action="delete"]').forEach((button) => {
      button.addEventListener('click', function () {
        const id = this.dataset.id;
        if (!confirm('Supprimer cette commande ?')) return;
        clearMessage();
        sendPost('admin-order-delete', { id, csrf_token: csrfToken })
          .then((payload) => {
            if (!payload.ok) throw new Error(payload.message || 'Erreur');
            showMessage(payload.message || 'Commande supprimée.', 'success');
            loadOrders();
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

  function formatDate(value) {
    const date = new Date(value);
    return isNaN(date.getTime()) ? '' : date.toLocaleString('fr-FR');
  }

  function capitalize(value) {
    return String(value || '').replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
  }

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  if (root) {
    loadOrders();
  }
});
