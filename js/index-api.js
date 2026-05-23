document.addEventListener('DOMContentLoaded', function () {
  const root = document.getElementById('home-featured');
  if (!root) return;

  function itemHTML(m) {
    return `<div class="featured-item"><h4>${escapeHtml(m.titre)}</h4><p>${escapeHtml(m.description || '')}</p><p><strong>${Number(m.prix).toFixed(2)} €</strong></p></div>`;
  }

  function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  fetch('api.php?resource=home')
    .then(r => r.json())
    .then(json => {
      if (!json.ok) throw new Error(json.error || 'API error');
      const data = json.data || [];
      if (!data.length) { root.innerHTML = '<p>Aucun menu mis en avant.</p>'; return; }
      root.innerHTML = data.map(itemHTML).join('\n');
    })
    .catch(err => { root.innerHTML = `<p class="error">Erreur: ${escapeHtml(err.message)}</p>`; });
});
