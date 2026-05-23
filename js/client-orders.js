document.addEventListener('DOMContentLoaded', function () {
  const apiUrl = '../public/api.php';
  const csrfToken = document.body.dataset.csrfToken;
  const menusRoot = document.getElementById('menus-root');
  const cartRoot = document.getElementById('cart-root');
  const checkoutForm = document.getElementById('checkout-form');

  let cart = JSON.parse(localStorage.getItem('vg_cart') || '[]');

  function saveCart() {
    localStorage.setItem('vg_cart', JSON.stringify(cart));
    renderCart();
  }

  function showMessage(container, text, type = 'success') {
    container.textContent = text;
    container.className = `alert alert-${type}`;
    container.style.display = 'block';
  }

  function fetchMenus() {
    fetch(`${apiUrl}?resource=menus-available`)
      .then((r) => r.json())
      .then((payload) => {
        if (!payload.ok) throw new Error(payload.message || 'Erreur API');
        renderMenus(payload.data || []);
      })
      .catch((e) => {
        if (menusRoot) menusRoot.innerHTML = '<p>Impossible de charger les menus.</p>';
      });
  }

  function renderMenus(menus) {
    if (!menusRoot) return;
    if (!menus.length) {
      menusRoot.innerHTML = '<p>Aucun menu disponible.</p>';
      return;
    }
    menusRoot.innerHTML = menus
      .map(
        (m) => `
      <div class="menu-card">
        <div class="menu-title">${escapeHtml(m.titre)}</div>
        <div class="menu-price">${Number(m.prix).toFixed(2)} €</div>
        <div class="menu-actions">
          <input type="number" min="1" value="1" class="qty-input" data-id="${m.id}">
          <button class="btn btn-primary btn-add" data-id="${m.id}">Ajouter</button>
        </div>
      </div>
    `
      )
      .join('');

    menusRoot.querySelectorAll('.btn-add').forEach((btn) => {
      btn.addEventListener('click', function () {
        const id = Number(this.dataset.id);
        const input = menusRoot.querySelector(`.qty-input[data-id="${id}"]`);
        const qty = Math.max(1, Number(input.value || 1));
        addToCart(id, qty);
      });
    });
  }

  function addToCart(menuId, qty) {
    const found = cart.find((i) => i.menu_id === menuId);
    if (found) found.quantity += qty;
    else cart.push({ menu_id: menuId, quantity: qty });
    saveCart();
  }

  function removeFromCart(menuId) {
    cart = cart.filter((i) => i.menu_id !== menuId);
    saveCart();
  }

  function renderCart() {
    if (!cartRoot) return;
    if (!cart.length) {
      cartRoot.innerHTML = '<p>Votre panier est vide.</p>';
      return;
    }
    // fetch current titles/prices for items
    Promise.all(cart.map((it) => fetch(`${apiUrl}?resource=admin-menu&id=${encodeURIComponent(it.menu_id)}`).then((r) => r.json())))
      .then((results) => {
        cartRoot.innerHTML = results
          .map((res, idx) => {
            const it = cart[idx];
            if (!res.ok) return '';
            const menu = res.data || {};
            const subtotal = Number(menu.prix || 0) * Number(it.quantity || 1);
            return `
              <div class="cart-item">
                <div>${escapeHtml(menu.titre || 'Item')} × ${it.quantity} (${subtotal.toFixed(2)} €)</div>
                <div><button class="btn btn-danger btn-remove" data-id="${it.menu_id}">Retirer</button></div>
              </div>
            `;
          })
          .join('');
        cartRoot.querySelectorAll('.btn-remove').forEach((b) => b.addEventListener('click', () => removeFromCart(Number(b.dataset.id))));
      })
      .catch(() => {
        cartRoot.innerHTML = '<p>Erreur lors du rendu du panier.</p>';
      });
  }

  if (checkoutForm) {
    checkoutForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!cart.length) return alert('Le panier est vide.');
      const form = new FormData(checkoutForm);
      const adresse = form.get('adresse') || '';
      if (!adresse) return alert('Adresse requise.');
      fetch(`${apiUrl}?resource=client-order`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart, adresse, csrf_token: csrfToken }),
      })
        .then((r) => r.json())
        .then((payload) => {
          if (!payload.ok) throw new Error(payload.message || 'Erreur');
          // success
          cart = [];
          saveCart();
          window.location.href = '../client/dashboard.php';
        })
        .catch((err) => alert(err.message || 'Erreur lors du passage de la commande.'));
    });
  }

  function escapeHtml(str) {
    return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // Init
  fetchMenus();
  renderCart();
});
