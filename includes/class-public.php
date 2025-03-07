<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Public {
    public function __construct() {
        add_shortcode( 'jde_kiosques', array( $this, 'display_kiosques' ) );
    }

    public function display_kiosques() {
        $kiosques = get_transient( 'jde_kiosques_list' );
        
        if ( false === $kiosques ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'jde_kiosques';
            $kiosques = $wpdb->get_results( "SELECT * FROM $table_name" );
            set_transient( 'jde_kiosques_list', $kiosques, 12 * HOUR_IN_SECONDS );
        }
        
        if ( empty( $kiosques ) ) {
            return '<p>' . __( 'Aucun kiosque disponible.', 'jde-kiosques' ) . '</p>';
        }
        
        ob_start();
        echo '<div class="jde-kiosques-list">';
        foreach ( $kiosques as $kiosque ) {
            echo '<div class="kiosque-item" aria-labelledby="kiosque-' . esc_attr( $kiosque->id ) . '-title">';
            echo '<h2 id="kiosque-' . esc_attr( $kiosque->id ) . '-title">' . esc_html( $kiosque->nom ) . '</h2>';
            echo '<p>' . esc_html( $kiosque->description ) . '</p>';
            echo '</div>';
        }
        echo '</div>';
        
        return ob_get_clean();
    }
}

new JDE_Kiosques_Public();
