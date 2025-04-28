document.addEventListener('DOMContentLoaded', () => {
    // Calcul dynamique du total
    const updateTotal = () => {
        const prix = parseFloat(document.getElementById('prix').value);
        const quantite = parseInt(document.getElementById('commande_nombre').value) || 0;
        document.getElementById('total').value = (prix * quantite).toFixed(2) + ' €';
    };

    document.getElementById('commande_nombre')?.addEventListener('input', updateTotal);

    // Gestion AJAX du formulaire
    document.getElementById('commandeForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const content = await response.text();
            
            if (response.status === 422) {
                document.getElementById('orderProductContent').innerHTML = content;
                // Réattacher les événements après mise à jour du DOM
                document.getElementById('commande_nombre')?.addEventListener('input', updateTotal);
                return;
            }

            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la soumission du formulaire');
        }
    });
});