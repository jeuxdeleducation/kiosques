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
     * Vérifie si l'utilisateur a accès aux paramètres du plugin.
     */
    public static function user_has_access() {
        if ( ! function_exists( 'wp_get_current_user' ) ) {
            require_once ABSPATH . 'wp-includes/pluggable.php';
        }

        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }
        
        $authorized_users = get_option( 'jde_kiosques_authorized_users', array() );
        $current_user_id = get_current_user_id();

        return in_array( $current_user_id, (array) $authorized_users );
    }

    /**
     * Enregistrement du menu principal dans l'administration WordPress.
     */
    public static function register_admin_menu() {
        if ( ! self::user_has_access() ) {
            return;
        }

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
        register_setting( 'jde_kiosques_settings_group', 'jde_kiosques_authorized_users', array( 'sanitize_callback' => array( __CLASS__, 'sanitize_users' ) ) );
    }

    /**
     * Nettoie et valide la liste des utilisateurs autorisés.
     */
    public static function sanitize_users( $input ) {
        return array_map( 'intval', (array) $input );
    }

    /**
     * Affichage de la page des paramètres.
     */
    public static function settings_page() {
        if ( ! self::user_has_access() ) {
            wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
        }

        $users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
        $authorized_users = get_option( 'jde_kiosques_authorized_users', array() );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Paramètres de JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'jde_kiosques_settings_group' );
                ?>
                <h2><?php esc_html_e( 'Gestion des accès', 'jde-kiosques' ); ?></h2>
                <label><?php esc_html_e( 'Utilisateurs autorisés :', 'jde-kiosques' ); ?></label><br>
                <select name="jde_kiosques_authorized_users[]" multiple style="width: 300px; height: 100px;">
                    <?php foreach ( $users as $user ) : ?>
                        <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( in_array( $user->ID, (array) $authorized_users ) ); ?>>
                            <?php echo esc_html( $user->display_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

JDE_Kiosques_Admin::init();
