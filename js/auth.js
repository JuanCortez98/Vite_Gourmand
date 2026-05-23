document.addEventListener('DOMContentLoaded', function () {
  const page = document.body.dataset.page;
  const form = document.querySelector('form[data-auth-form]');
  const messageBox = document.getElementById('auth-message');
  const csrfToken = document.body.dataset.csrfToken;

  function setMessage(text, type = 'error') {
    if (!messageBox) return;
    messageBox.textContent = text;
    messageBox.className = type === 'success' ? 'auth-message success' : 'auth-message error';
  }

  function sendAuthRequest(resource, payload) {
    return fetch('../public/api.php?resource=' + resource, {
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).then((response) => response.json());
  }

  if (!form) return;

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.csrf_token = csrfToken;

    const resource = page === 'register' ? 'auth-register' : 'auth-login';
    setMessage('Envoi en cours…', 'success');

    sendAuthRequest(resource, data)
      .then((json) => {
        if (!json.ok) {
          setMessage(json.message || 'Erreur lors de la demande.');
          return;
        }

        if (page === 'register') {
          setMessage('Inscription réussie. Vous pouvez maintenant vous connecter.', 'success');
          form.reset();
          return;
        }

        const role = json.data?.role || 'client';
        if (role === 'admin') {
          window.location.href = '../admin/index.php';
        } else if (role === 'travailleur') {
          window.location.href = '../travailleur/dashboard.php';
        } else {
          window.location.href = '../client/dashboard.php';
        }
      })
      .catch((error) => {
        setMessage('Erreur réseau : ' + (error.message || 'Veuillez réessayer.'));
      });
  });
});
