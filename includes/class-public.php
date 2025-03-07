<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Public {
    public function __construct() {
        add_shortcode( 'jde_kiosques', array( $this, 'display_kiosques' ) );
    }

    public function display_kiosques() {
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
