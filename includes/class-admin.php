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
            __( 'JDE Kiosques', 'jde-kiosques' ),
            __( 'JDE Kiosques', 'jde-kiosques' ),
            'manage_options',
            'jde-kiosques',
            array( __CLASS__, 'settings_page' ),
            'dashicons-store',
            25
        );
        
        add_options_page(
            __( 'Paramètres JDE Kiosques', 'jde-kiosques' ),
            __( 'JDE Kiosques', 'jde-kiosques' ),
            'manage_options',
            'jde-kiosques-settings',
            array( __CLASS__, 'settings_page' )
        );
    }

    /**
     * Affichage de la page des paramètres.
     */
    public static function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
        }

        $total_kiosques = get_option( 'jde_kiosques_total', 10 );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Paramètres de JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <form method="post" action="admin-post.php">
                <?php wp_nonce_field( 'jde_kiosques_save_settings', 'jde_kiosques_settings_nonce' ); ?>
                <input type="hidden" name="action" value="save_jde_kiosques_settings">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="total_kiosques">Nombre total de kiosques</label></th>
                        <td><input type="number" id="total_kiosques" name="total_kiosques" value="<?php echo esc_attr( $total_kiosques ); ?>" min="1"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Sauvegarde des paramètres du plugin.
     */
    public static function save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
        }

        check_admin_referer( 'jde_kiosques_save_settings', 'jde_kiosques_settings_nonce' );
        
        if ( isset( $_POST['total_kiosques'] ) ) {
            update_option( 'jde_kiosques_total', intval( $_POST['total_kiosques'] ) );
        }
        
        wp_redirect( admin_url( 'admin.php?page=jde-kiosques&updated=true' ) );
        exit;
    }
}

JDE_Kiosques_Admin::init();
