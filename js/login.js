function gererReponseCredential(response) {
    console.log("Token Google reçu :", response.credential);

    fetch('http://127.0.0.1:300/api/login.google', { // ← à changer pour ton vrai endpoint
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ credential: response.credential })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('token', data.token);
            // Redirection selon le rôle
            if (role === 'admin') window.location.href = 'admin.html';
            else if (role === 'travailleur') window.location.href = 'travailleur.html';
            else window.location.href = 'client.html';
        } else {
            alert('Erreur de connexion avec Google : ' + (data.message || 'Réessayez plus tard'));
        }
    })
    .catch(err => {
        console.error('Erreur lors de l\'envoi du token Google', err);
        alert('La connexion avec Google n\'a pas pu être effectuée');
    });
}