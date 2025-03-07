<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Settings {
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function add_settings_page() {
        add_options_page(
            __( 'Paramètres JDE Kiosques', 'jde-kiosques' ),
            __( 'JDE Kiosques', 'jde-kiosques' ),
            'manage_options',
            'jde-kiosques-settings',
            array( __CLASS__, 'settings_page' )
        );
    }

    public static function register_settings() {
        register_setting( 'jde_kiosques_settings_group', 'jde_kiosques_total', 'intval' );
        
        add_settings_section(
            'jde_kiosques_main_section',
            __( 'Réglages principaux', 'jde-kiosques' ),
            null,
            'jde-kiosques-settings'
        );
        
        add_settings_field(
            'jde_kiosques_total',
            __( 'Nombre total de kiosques', 'jde-kiosques' ),
            array( __CLASS__, 'total_kiosques_field' ),
            'jde-kiosques-settings',
            'jde_kiosques_main_section'
        );
    }

    public static function total_kiosques_field() {
        $value = get_option( 'jde_kiosques_total', 10 );
        echo '<input type="number" name="jde_kiosques_total" value="' . esc_attr( $value ) . '" min="1">';
    }

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
                do_settings_sections( 'jde-kiosques-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

JDE_Kiosques_Settings::init();
