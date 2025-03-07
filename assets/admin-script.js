// assets/admin-script.js
jQuery(document).ready(function($) {
    if (typeof $.fn.draggable === 'undefined') {
        console.error('jQuery UI Draggable non chargé.');
        return;
    }

    var $hotspots = $('#positionnement-container .kiosque-hotspot.admin');
    
    // Rendre les hotspots déplaçables
    $hotspots.draggable({
        containment: '#positionnement-container',
        stop: function() {
            console.log('Position sauvegardée:', $(this).position());
        }
    });
    
    // Bouton de sauvegarde
    $('#save-positions').on('click', function(e) {
        e.preventDefault();
        var positions = {};
        
        $hotspots.each(function() {
            var $this = $(this);
            positions[$this.data('id')] = {
                left: ($this.position().left / $('#positionnement-container').width()) * 100 + '%',
                top: ($this.position().top / $('#positionnement-container').height()) * 100 + '%'
            };
        });
        
        console.log('Positions enregistrées:', positions);
        
        $('#save-positions').text('Sauvegarde...');
        
        $.post(jdeKiosquesAjax.ajax_url, {
            action: 'save_positions',
            positions: positions,
            security: jdeKiosquesAjax.nonce
        }, function(response) {
            $('#save-positions').text('Enregistrer');
            if (response.success) {
                alert('Positions sauvegardées avec succès!');
            } else {
                alert('Erreur lors de la sauvegarde.');
                console.error(response);
            }
        });
    });
});
