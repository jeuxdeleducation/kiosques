<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Ajax {
    public function __construct() {
        add_action( 'wp_ajax_jde_kiosques_reserve', array( $this, 'reserve_kiosk' ) );
        add_action( 'wp_ajax_nopriv_jde_kiosques_reserve', array( $this, 'reserve_kiosk' ) );
        
        add_action( 'wp_ajax_jde_kiosques_confirm', array( $this, 'confirm_reservation' ) );
        add_action( 'wp_ajax_jde_kiosques_cancel', array( $this, 'cancel_reservation' ) );
        
        add_action( 'wp_ajax_jde_kiosques_save_positions', array( $this, 'save_positions' ) );
    }
    
    public function reserve_kiosk() {
        check_ajax_referer( 'jde_kiosques_nonce', 'nonce' );

        if ( ! isset( $_POST['kiosk_number'], $_POST['access_code'], $_POST['company_name'] ) ) {
            wp_send_json_error( __( 'Données manquantes.', 'jde-kiosques' ) );
        }

        $kiosk_number = intval( $_POST['kiosk_number'] );
        $access_code  = sanitize_text_field( $_POST['access_code'] );
        $company_name = sanitize_text_field( $_POST['company_name'] );

        if ( ! $kiosk_number || empty( $access_code ) || empty( $company_name ) ) {
            wp_send_json_error( __( 'Informations invalides.', 'jde-kiosques' ) );
        }

        $db = new JDE_Kiosques_Database();
        if ( $db->get_reservation_by_kiosk( $kiosk_number ) ) {
            wp_send_json_error( __( 'Ce kiosque est déjà réservé.', 'jde-kiosques' ) );
        }

        if ( $db->add_reservation( $kiosk_number, $company_name, $access_code ) ) {
            JDE_Kiosques_Logs::add_log( "Réservation créée pour le kiosque {$kiosk_number} par {$company_name}" );
            wp_send_json_success( __( 'Réservation en attente de validation.', 'jde-kiosques' ) );
        }

        wp_send_json_error( __( 'Erreur lors de la réservation.', 'jde-kiosques' ) );
    }
    
    public function confirm_reservation() {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_jde_kiosques' ) ) {
            wp_send_json_error( __( 'Accès refusé.', 'jde-kiosques' ) );
        }
        check_ajax_referer( 'jde_kiosques_nonce', 'nonce' );
        $reservation_id = isset( $_POST['reservation_id'] ) ? intval( $_POST['reservation_id'] ) : 0;
        if ( ! $reservation_id ) {
            wp_send_json_error( __( 'ID de réservation manquant.', 'jde-kiosques' ) );
        }
        $db = new JDE_Kiosques_Database();
        if ( $db->update_reservation_status( $reservation_id, 'confirme' ) !== false ) {
            JDE_Kiosques_Logs::add_log( "Réservation ID {$reservation_id} confirmée." );
            wp_send_json_success( __( 'Réservation confirmée.', 'jde-kiosques' ) );
        }
        wp_send_json_error( __( 'Erreur lors de la validation.', 'jde-kiosques' ) );
    }
    
    public function cancel_reservation() {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_jde_kiosques' ) ) {
            wp_send_json_error( __( 'Accès refusé.', 'jde-kiosques' ) );
        }
        check_ajax_referer( 'jde_kiosques_nonce', 'nonce' );
        $reservation_id = isset( $_POST['reservation_id'] ) ? intval( $_POST['reservation_id'] ) : 0;
        if ( ! $reservation_id ) {
            wp_send_json_error( __( 'ID de réservation manquant.', 'jde-kiosques' ) );
        }
        $db = new JDE_Kiosques_Database();
        if ( $db->update_reservation_status( $reservation_id, 'annule' ) !== false ) {
            JDE_Kiosques_Logs::add_log( "Réservation ID {$reservation_id} annulée." );
            wp_send_json_success( __( 'Réservation annulée.', 'jde-kiosques' ) );
        }
        wp_send_json_error( __( 'Erreur lors de l'annulation.', 'jde-kiosques' ) );
    }
    
    public function save_positions() {
        if ( ! current_user_can( 'manage_jde_kiosques' ) ) {
            wp_send_json_error( __( 'Accès refusé.', 'jde-kiosques' ) );
        }
        check_ajax_referer( 'jde_kiosques_nonce', 'nonce' );
        $positions = isset( $_POST['positions'] ) ? $_POST['positions'] : array();
        if ( ! is_array( $positions ) ) {
            wp_send_json_error( __( 'Données invalides.', 'jde-kiosques' ) );
        }
        update_option( 'jde_kiosques_positions', $positions );
        wp_send_json_success( __( 'Positions enregistrées.', 'jde-kiosques' ) );
    }
}
new JDE_Kiosques_Ajax();
