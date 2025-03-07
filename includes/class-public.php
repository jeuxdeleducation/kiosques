<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Public {
    public function __construct() {
        add_shortcode( 'jde_kiosques', array( $this, 'display_kiosques' ) );
    }

    /**
     * Vérifie si l'utilisateur a accès aux fonctionnalités publiques.
     */
    public function user_has_access() {
        if ( ! function_exists( 'wp_get_current_user' ) ) {
            require_once ABSPATH . 'wp-includes/pluggable.php';
        }
        
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }
        
        $authorized_users = get_option( 'jde_kiosques_authorized_users', array() );
        return in_array( get_current_user_id(), (array) $authorized_users );
    }

    /**
     * Affiche la liste des kiosques.
     */
    public function display_kiosques() {
        if ( ! $this->user_has_access() ) {
            return '<div class="notice notice-error"><p>' . __( 'Vous n’avez pas la permission de voir cette section.', 'jde-kiosques' ) . '</p></div>';
        }
        
        $force_refresh = isset( $_GET['refresh_kiosques'] ) && current_user_can( 'manage_options' );
        
        $kiosques = wp_cache_get( 'jde_kiosques_list' );
        
        if ( false === $kiosques || $force_refresh ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'jde_kiosques_reservations';
            $kiosques = $wpdb->get_results( "SELECT * FROM $table_name" );
            
            wp_cache_set( 'jde_kiosques_list', $kiosques, '', 300 ); // Cache pendant 5 minutes
        }
        
        if ( empty( $kiosques ) ) {
            return '<p>' . __( 'Aucun kiosque disponible.', 'jde-kiosques' ) . '</p>';
        }
        
        $output = '<div class="kiosques-list">';
        foreach ( $kiosques as $kiosque ) {
            $output .= '<div class="kiosque-item">';
            $output .= '<strong>' . esc_html( $kiosque->kiosk_number ) . '</strong> - ' . esc_html( $kiosque->partner_code );
            $output .= '</div>';
        }
        $output .= '</div>';
        
        return $output;
    }
}

new JDE_Kiosques_Public();
