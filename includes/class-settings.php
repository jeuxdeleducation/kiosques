<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Settings {
    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    /**
     * Enregistre les paramètres du plugin avec l'API WordPress.
     */
    public static function register_settings() {
        register_setting( 'jde_kiosques_settings_group', 'jde_kiosques_total', 'intval' );
        register_setting( 'jde_kiosques_settings_group', 'jde_kiosques_authorized_users', array( 'sanitize_callback' => array( __CLASS__, 'sanitize_users' ) ) );
    }

    /**
     * Nettoie et valide la liste des utilisateurs autorisés.
     */
    public static function sanitize_users( $input ) {
        return array_map( 'intval', (array) $input );
    }

    /**
     * Affichage de la section des paramètres.
     */
    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Paramètres de JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <?php if ( ! JDE_Kiosques_Admin::user_has_access() ) : ?>
                <div class="notice notice-error"><p><?php esc_html_e( 'Vous n’avez pas la permission d’accéder à cette page.', 'jde-kiosques' ); ?></p></div>
            <?php else : ?>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'jde_kiosques_settings_group' );
                    do_settings_sections( 'jde-kiosques' );
                    submit_button();
                    ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }
}

JDE_Kiosques_Settings::init();
