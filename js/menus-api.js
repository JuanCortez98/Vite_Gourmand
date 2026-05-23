document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('menus-root');
  if (!root) return;

  function cardHTML(menu) {
    const desc = menu.description ? `<p>${escapeHtml(menu.description)}</p>` : '';
    return `
      <article class="menu-card">
        <h3>${escapeHtml(menu.titre)}</h3>
        ${desc}
        <p><strong>Prix:</strong> ${Number(menu.prix).toFixed(2)} €</p>
        <a href="menu.php?id=${encodeURIComponent(menu.id)}" class="btn">Voir</a>
      </article>
    `;
  }

  function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  fetch('api.php?resource=menus')
    .then(r => r.json())
    .then(json => {
      if (!json.ok) throw new Error(json.error || 'API error');
      const data = json.data || [];
      if (!data.length) {
        root.innerHTML = '<p>Aucun menu disponible pour le moment.</p>';
        return;
      }
      root.innerHTML = data.map(cardHTML).join('\n');
    })
    .catch(err => {
      root.innerHTML = `<p class="error">Erreur: ${escapeHtml(err.message)}</p>`;
    });
});
