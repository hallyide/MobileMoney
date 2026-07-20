(function () {
    'use strict';

    /** Ouvre une fenetre modale du template. */
    function ouvrirModale(id) {
        const modale = document.getElementById(id);
        if (modale) {
            modale.classList.add('open');

            // Place le curseur dans le premier champ de la modale.
            const premierChamp = modale.querySelector('input, select, textarea, button');
            if (premierChamp) {
                premierChamp.focus();
            }
        }
    }

    /** Ferme une fenetre modale du template. */
    function fermerModale(modale) {
        if (modale) {
            modale.classList.remove('open');
        }
    }

    document.addEventListener('click', function (evenement) {
        const boutonOuverture = evenement.target.closest('[data-open-modal]');
        if (boutonOuverture) {
            ouvrirModale(boutonOuverture.dataset.openModal);
            return;
        }

        const boutonFermeture = evenement.target.closest('[data-close-modal]');
        if (boutonFermeture) {
            fermerModale(boutonFermeture.closest('.modal-overlay'));
            return;
        }

        // Un clic sur le fond sombre ferme egalement la modale.
        if (evenement.target.classList.contains('modal-overlay')) {
            fermerModale(evenement.target);
        }
    });

    document.addEventListener('keydown', function (evenement) {
        if (evenement.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(fermerModale);
        }
    });

    // Les messages flash PHP disparaissent apres quelques secondes.
    window.setTimeout(function () {
        document.querySelectorAll('.toast-stack').forEach(function (notification) {
            notification.remove();
        });
    }, 5000);
})();
