<?php
/*
Plugin Name: JDE Kiosques
Description: Plugin de gestion des kiosques pour Jeux de l'Éducation.
Version: 1.2
Author: Samuel Lavoie
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Enqueue des scripts front-end **/
function jde_kiosques_enqueue_scripts() {
    wp_enqueue_script(
        'jde-kiosques-script',
        plugins_url( 'assets/script.js', __FILE__ ),
        array('jquery'),
        '1.1',
        true
    );
    // Localisation du script pour AJAX
    wp_localize_script(
        'jde-kiosques-script',
        'jdeKiosquesAjax',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jde-kiosques-nonce' )
        )
    );
}
add_action( 'wp_enqueue_scripts', 'jde_kiosques_enqueue_scripts' );

/** Inclusion du fichier d'administration (uniquement dans l'admin) **/
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin.php';
    JDE_Kiosques_Admin::init();
}

/** Gestion de l'AJAX côté front-end pour réserver un kiosque **/
function jde_kiosques_reserve_kiosk() {
    check_ajax_referer( 'jde-kiosques-nonce', 'security' );
    
    $kiosk_number = isset( $_POST['kiosk_number'] ) ? intval( $_POST['kiosk_number'] ) : 0;
    $partner_code = isset( $_POST['partner_code'] ) ? sanitize_text_field( $_POST['partner_code'] ) : '';

    if ( $kiosk_number > 0 && ! empty( $partner_code ) ) {
        // Exemple simple : sauvegarde dans une option.
        $reservations = get_option( 'jde_kiosques_reservations', array() );
        $reservations[] = array(
            'kiosk_number' => $kiosk_number,
            'partner_code' => $partner_code,
            'date'         => current_time( 'mysql' )
        );
        update_option( 'jde_kiosques_reservations', $reservations );

        $response = array(
            'success' => true,
            'message' => 'Réservation pour le kiosque #' . $kiosk_number . ' enregistrée avec succès!'
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Erreur : numéro de kiosque ou code partenaire manquant.'
        );
    }
    wp_send_json( $response );
}
add_action( 'wp_ajax_reserve_kiosk', 'jde_kiosques_reserve_kiosk' );
add_action( 'wp_ajax_nopriv_reserve_kiosk', 'jde_kiosques_reserve_kiosk' );
