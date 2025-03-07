// assets/script.js
jQuery(document).ready(function($) {
    $('.kiosk').on('click', function() {
        var kioskID = $(this).data('id');
        var partnerCode = $('#partner_code').val();

        if (!kioskID || !partnerCode) {
            alert('Veuillez entrer un code partenaire avant de réserver un kiosque.');
            return;
        }
        
        console.log('Tentative de réservation du kiosque:', kioskID, 'avec le code:', partnerCode);
        
        var $this = $(this);
        $this.prop('disabled', true).text('Réservation en cours...');

        $.post(jdeKiosquesAjax.ajax_url, {
            action: 'reserve_kiosk',
            kiosk_number: kioskID,
            partner_code: partnerCode,
            security: jdeKiosquesAjax.nonce
        }, function(response) {
            $this.prop('disabled', false).text('Réserver');
            
            if (response.success) {
                alert('Kiosque réservé avec succès!');
                location.reload();
            } else {
                alert('Erreur: ' + (response.data ? response.data.message : 'Une erreur est survenue.'));
                console.error(response);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $this.prop('disabled', false).text('Réserver');
            alert('Erreur de connexion: ' + textStatus);
            console.error('Détails de l'erreur:', errorThrown);
        });
    });
});
