<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Admin {
    /**
     * Initialise l'administration en enregistrant les menus et les paramètres.
     */
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    /**
     * Enregistrement du menu principal dans l'administration WordPress.
     */
    public static function register_admin_menu() {
        add_menu_page(
            __( 'JDE Kiosques', 'jde-kiosques' ),
            __( 'JDE Kiosques', 'jde-kiosques' ),
            'manage_options',
            'jde-kiosques',
            array( __CLASS__, 'settings_page' ),
            'dashicons-store',
            25
        );
    }

    /**
     * Enregistre les paramètres du plugin avec l'API WordPress.
     */
    public static function register_settings() {
        register_setting( 'jde_kiosques_settings_group', 'jde_kiosques_total', 'intval' );
    }

    /**
     * Affichage de la page des paramètres.
     */
    public static function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Paramètres de JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'jde_kiosques_settings_group' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

JDE_Kiosques_Admin::init();
