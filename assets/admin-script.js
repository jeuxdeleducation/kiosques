// assets/admin-script.js

jQuery(document).ready(function($) {
    // Rendre les hotspots (version admin) déplaçables dans le conteneur
    $('#positionnement-container .kiosque-hotspot.admin').draggable({
        containment: "#positionnement-container"
    });
    
    // Sauvegarder les positions lors du clic sur le bouton
    $('#save-positions').on('click', function(e) {
        e.preventDefault();
        var positions = {};
        // Pour chaque hotspot, calculer la position en pourcentage relative au conteneur
        $('#positionnement-container .kiosque-hotspot.admin').each(function() {
            var $this = $(this);
            var kioskId = $this.data('kiosk');
            var containerWidth = $('#positionnement-container').width();
            var containerHeight = $('#positionnement-container').height();
            var offset = $this.position();
            var posXPercent = (offset.left / containerWidth) * 100;
            var posYPercent = (offset.top / containerHeight) * 100;
            positions[kioskId] = {
                x: posXPercent,
                y: posYPercent
            };
        });
        
        // Envoyer les positions via AJAX
        $.ajax({
            url: jdeKiosquesAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'jde_kiosques_save_positions',
                nonce: jdeKiosquesAjax.nonce,
                positions: positions
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Erreur lors de la sauvegarde des positions.');
            }
        });
    });
});
