document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('client-dashboard-root');
  if (!root) return;

  fetch('../public/api.php?resource=client-orders')
    .then((response) => response.json())
    .then((json) => {
      if (!json.ok) {
        root.innerHTML = `<p class="error">${json.message || 'Erreur lors du chargement des commandes.'}</p>`;
        return;
      }
      const orders = json.data;
      if (!orders.length) {
        root.innerHTML = '<p>Aucune commande trouvée.</p>';
        return;
      }
      root.innerHTML = `
        <table class="orders-table">
          <thead><tr><th>ID</th><th>Date</th><th>Total</th><th>Statut</th><th>Adresse</th></tr></thead>
          <tbody>${orders.map((order) => `
            <tr>
              <td>${order.id}</td>
              <td>${new Date(order.created_at).toLocaleString('fr-FR')}</td>
              <td>${Number(order.total).toFixed(2)} €</td>
              <td>${order.status}</td>
              <td>${order.adresse_livraison || 'N/A'}</td>
            </tr>
          `).join('')}</tbody>
        </table>
      `;
    })
    .catch((error) => {
      root.innerHTML = `<p class="error">Erreur réseau : ${error.message}</p>`;
    });
});
