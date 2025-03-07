<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Settings {
    public static function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
        }
        
        if ( isset( $_POST['jde_kiosques_settings_nonce'] ) && wp_verify_nonce( $_POST['jde_kiosques_settings_nonce'], 'jde_kiosques_save_settings' ) ) {
            $total = isset( $_POST['total_kiosques'] ) ? intval( $_POST['total_kiosques'] ) : 20;
            update_option( 'jde_kiosques_total', $total );
            
            $restrict = isset( $_POST['restrict_access'] ) ? 1 : 0;
            update_option( 'jde_kiosques_restrict_access', $restrict );
            
            $plan_image = isset( $_POST['plan_image'] ) ? esc_url_raw( $_POST['plan_image'] ) : '';
            update_option( 'jde_kiosques_plan_image', $plan_image );
            
            add_settings_error( 'jde_kiosques_settings', 'settings_updated', __( 'Paramètres mis à jour.', 'jde-kiosques' ), 'updated' );
        }
        
        $total_kiosques  = intval( get_option( 'jde_kiosques_total', 20 ) );
        $restrict_access = intval( get_option( 'jde_kiosques_restrict_access', 0 ) );
        $plan_image      = esc_url( get_option( 'jde_kiosques_plan_image', '' ) );
        ?>
        <div class="wrap">
            <h1><?php _e( 'Configuration JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <?php settings_errors( 'jde_kiosques_settings' ); ?>
            <form method="post" action="">
                <?php wp_nonce_field( 'jde_kiosques_save_settings', 'jde_kiosques_settings_nonce' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Nombre total de kiosques', 'jde-kiosques' ); ?></th>
                        <td>
                            <input type="number" name="total_kiosques" value="<?php echo esc_attr( $total_kiosques ); ?>" min="1" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Restreindre l'accès aux organisateurs uniquement', 'jde-kiosques' ); ?></th>
                        <td>
                            <input type="checkbox" name="restrict_access" value="1" <?php checked( $restrict_access, 1 ); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'URL de l'image du plan', 'jde-kiosques' ); ?></th>
                        <td>
                            <input type="url" name="plan_image" value="<?php echo esc_attr( $plan_image ); ?>" style="width: 60%;" />
                            <p class="description">
                                <?php _e( 'Entrez l'URL de l'image représentant le plan.', 'jde-kiosques' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
