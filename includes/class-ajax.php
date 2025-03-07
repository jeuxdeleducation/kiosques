<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Ajax {
    public function __construct() {
        add_action( 'wp_ajax_jde_kiosques_reserve', array( $this, 'reserve_kiosk' ) );
        add_action( 'wp_ajax_nopriv_jde_kiosques_reserve', array( $this, 'reserve_kiosk' ) );
    }

    /**
     * Réservation d'un kiosque via AJAX.
     */
    public function reserve_kiosk() {
        check_ajax_referer( 'jde_kiosques_nonce', 'security' );
        
        if ( ! isset( $_POST['kiosk_number'] ) || ! isset( $_POST['partner_code'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Données manquantes.', 'jde-kiosques' ) ) );
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'jde_kiosques_reservations';
        $kiosk_number = intval( $_POST['kiosk_number'] );
        $partner_code = sanitize_text_field( $_POST['partner_code'] );
        
        // Vérifier si le kiosque est déjà réservé
        $exists = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE kiosk_number = %d", $kiosk_number) );
        
        if ( $exists ) {
            wp_send_json_error( array( 'message' => __( 'Ce kiosque est déjà réservé.', 'jde-kiosques' ) ) );
        }
        
        // Insérer la réservation
        $result = $wpdb->insert(
            $table_name,
            array(
                'kiosk_number' => $kiosk_number,
                'partner_code' => $partner_code,
                'reserved_at' => current_time( 'mysql' )
            ),
            array('%d', '%s', '%s')
        );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Kiosque réservé avec succès!', 'jde-kiosques' ) ) );
        } else {
            error_log( 'Erreur de réservation du kiosque ' . $kiosk_number );
            wp_send_json_error( array( 'message' => __( 'Erreur lors de la réservation.', 'jde-kiosques' ) ) );
        }
    }
}

new JDE_Kiosques_Ajax();
