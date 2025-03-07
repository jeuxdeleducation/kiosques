<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Admin {
    /**
     * Initialise l'administration en enregistrant les menus et l'action de sauvegarde.
     */
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_admin_menu' ) );
        add_action( 'admin_post_save_jde_kiosques_settings', array( __CLASS__, 'save_settings' ) );
    }

    /**
     * Enregistrement des menus dans l'administration WordPress.
     */
    public static function register_admin_menu() {
        add_menu_page(
            'JDE Kiosques',
            'JDE Kiosques',
            'manage_options',
            'jde-kiosques',
            array( __CLASS__, 'admin_main_page' ),
            'dashicons-welcome-write-blog',
            6
        );
        add_submenu_page(
            'jde-kiosques',
            'Réservations',
            'Réservations',
            'manage_options',
            'jde-kiosques-reservations',
            array( __CLASS__, 'admin_reservations_page' )
        );
        add_submenu_page(
            'jde-kiosques',
            'Codes Partenaires',
            'Codes Partenaires',
            'manage_options',
            'jde-kiosques-partner-codes',
            array( __CLASS__, 'admin_partner_codes_page' )
        );
    }

    /**
     * Page principale du plugin.
     */
    public static function admin_main_page() {
        ?>
        <div class="wrap">
            <h1>JDE Kiosques - Tableau de bord</h1>
            <p>Bienvenue dans l'administration du plugin JDE Kiosques.</p>
        </div>
        <?php
    }

    /**
     * Sauvegarde les données des formulaires.
     */
    public static function save_settings() {
        if ( ! isset( $_POST['jde_kiosques_nonce'] ) || ! wp_verify_nonce( $_POST['jde_kiosques_nonce'], 'jde_kiosques_settings' ) ) {
            wp_die( 'Permission refusée' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Permission refusée' );
        }

        if ( isset( $_POST['kiosk_number'] ) && isset( $_POST['partner_code'] ) ) {
            $kiosk_number = intval( $_POST['kiosk_number'] );
            $partner_code = sanitize_text_field( $_POST['partner_code'] );
            $reservations = get_option( 'jde_kiosques_reservations', array() );
            $reservations[] = array(
                'kiosk_number' => $kiosk_number,
                'partner_code' => $partner_code,
                'date'         => current_time( 'mysql' )
            );
            update_option( 'jde_kiosques_reservations', $reservations );
        }

        wp_safe_redirect( admin_url( 'admin.php?page=jde-kiosques' ) );
        exit;
    }
}
