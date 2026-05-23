document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('admin-dashboard-root');
  if (!root) return;

  fetch('../public/api.php?resource=admin-stats')
    .then((response) => response.json())
    .then((json) => {
      if (!json.ok) {
        root.innerHTML = `<p class="error">${json.message || 'Erreur lors du chargement des statistiques.'}</p>`;
        return;
      }
      const stats = json.data;
      root.innerHTML = `
        <div class="stats-grid">
          <div class="stat-card"><h3>Utilisateurs</h3><p>${stats.totalUsers}</p></div>
          <div class="stat-card"><h3>Commandes</h3><p>${stats.totalOrders}</p></div>
          <div class="stat-card"><h3>En cours</h3><p>${stats.ordersInProgress}</p></div>
          <div class="stat-card"><h3>Stock bas</h3><p>${stats.menusLowStock}</p></div>
        </div>
      `;
    })
    .catch((error) => {
      root.innerHTML = `<p class="error">Erreur réseau : ${error.message}</p>`;
    });
});
