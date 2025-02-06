jQuery(document).ready(function($) {
    $('.kiosk').click(function() {
        var kioskID = $(this).data('id');
        var partnerCode = $('#partner_code').val();

        $.post(jdeKiosquesAjax.ajax_url, {
            action: 'reserve_kiosk',
            kiosk_number: kioskID,
            partner_code: partnerCode,
            security: jdeKiosquesAjax.nonce
        }, function(response) {
            alert(response.message);
            if (response.success) {
                location.reload();
            }
        });
    });
});
