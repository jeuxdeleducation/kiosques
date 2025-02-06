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
        // Action pour sauvegarder les réglages via les formulaires admin.
        add_action( 'admin_post_save_jde_kiosques_settings', array( __CLASS__, 'save_settings' ) );
    }

    /**
     * Enregistrement des menus dans l'administration WordPress.
     */
    public static function register_admin_menu() {
        // Page principale du plugin.
        add_menu_page(
            'JDE Kiosques',
            'JDE Kiosques',
            'manage_options',
            'jde-kiosques',
            array( __CLASS__, 'admin_main_page' ),
            'dashicons-welcome-write-blog',
            6
        );
        // Sous-menu pour gérer les réservations.
        add_submenu_page(
            'jde-kiosques',
            'Réservations',
            'Réservations',
            'manage_options',
            'jde-kiosques-reservations',
            array( __CLASS__, 'admin_reservations_page' )
        );
        // Sous-menu pour gérer les codes partenaires.
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
     * Affiche la page principale du plugin dans l'admin.
     */
    public static function admin_main_page() {
        ?>
        <div class="wrap">
            <h1>JDE Kiosques - Tableau de bord</h1>
            <p>Bienvenue dans l'administration du plugin JDE Kiosques.</p>
            <ul>
                <li><a href="<?php echo admin_url('admin.php?page=jde-kiosques-reservations'); ?>">Gérer les Réservations</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=jde-kiosques-partner-codes'); ?>">Gérer les Codes Partenaires</a></li>
            </ul>
        </div>
        <?php
    }

    /**
     * Affiche la page de gestion des réservations.
     */
    public static function admin_reservations_page() {
        // Récupération des réservations enregistrées.
        $reservations = get_option( 'jde_kiosques_reservations', array() );
        ?>
        <div class="wrap">
            <h1>Gérer les Réservations</h1>
            <h2>Ajouter une Réservation Manuelle</h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field( 'jde_kiosques_settings', 'jde_kiosques_nonce' ); ?>
                <input type="hidden" name="action" value="save_jde_kiosques_settings">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="kiosk_number">Numéro du Kiosque</label></th>
                        <td><input name="kiosk_number" type="number" id="kiosk_number" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partner_code">Code Partenaire</label></th>
                        <td><input name="partner_code" type="text" id="partner_code" class="regular-text"></td>
                    </tr>
                </table>
                <?php submit_button('Ajouter la Réservation'); ?>
            </form>
            <h2>Liste des Réservations</h2>
            <?php
            if ( ! empty( $reservations ) ) {
                echo '<table class="widefat fixed"><thead><tr><th>Numéro du Kiosque</th><th>Code Partenaire</th><th>Date</th></tr></thead><tbody>';
                foreach ( $reservations as $reservation ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $reservation['kiosk_number'] ) . '</td>';
                    echo '<td>' . esc_html( $reservation['partner_code'] ) . '</td>';
                    echo '<td>' . esc_html( $reservation['date'] ) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucune réservation enregistrée pour le moment.</p>';
            }
            ?>
        </div>
        <?php
    }

    /**
     * Affiche la page de gestion des codes partenaires.
     */
    public static function admin_partner_codes_page() {
        // Récupération des codes partenaires enregistrés.
        $partner_codes = get_option( 'jde_kiosques_partner_codes', array() );
        ?>
        <div class="wrap">
            <h1>Gérer les Codes Partenaires</h1>
            <h2>Créer un Nouveau Code Partenaire</h2>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field( 'jde_kiosques_settings', 'jde_kiosques_nonce' ); ?>
                <input type="hidden" name="action" value="save_jde_kiosques_settings">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="new_partner_code">Code Partenaire</label></th>
                        <td><input name="new_partner_code" type="text" id="new_partner_code" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="partner_description">Description</label></th>
                        <td><textarea name="partner_description" id="partner_description" rows="5" class="large-text"></textarea></td>
                    </tr>
                </table>
                <?php submit_button('Créer le Code Partenaire'); ?>
            </form>
            <h2>Liste des Codes Partenaires</h2>
            <?php
            if ( ! empty( $partner_codes ) ) {
                echo '<table class="widefat fixed"><thead><tr><th>Code</th><th>Description</th><th>Date</th></tr></thead><tbody>';
                foreach ( $partner_codes as $code ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $code['code'] ) . '</td>';
                    echo '<td>' . esc_html( $code['description'] ) . '</td>';
                    echo '<td>' . esc_html( $code['date'] ) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>Aucun code partenaire enregistré pour le moment.</p>';
            }
            ?>
        </div>
        <?php
    }

    /**
     * Fonction de sauvegarde des réglages (formulaires de réservation et codes partenaires)
     */
    public static function save_settings() {
        // Vérification du nonce
        if ( ! isset( $_POST['jde_kiosques_nonce'] ) || ! wp_verify_nonce( $_POST['jde_kiosques_nonce'], 'jde_kiosques_settings' ) ) {
            wp_die( 'Permission refusée' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Permission refusée' );
        }

        // Si le formulaire de réservation a été soumis
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

        // Si le formulaire de code partenaire a été soumis
        if ( isset( $_POST['new_partner_code'] ) ) {
            $new_partner_code    = sanitize_text_field( $_POST['new_partner_code'] );
            $partner_description = isset( $_POST['partner_description'] ) ? sanitize_textarea_field( $_POST['partner_description'] ) : '';

            $partner_codes = get_option( 'jde_kiosques_partner_codes', array() );
            $partner_codes[] = array(
                'code'        => $new_partner_code,
                'description' => $partner_description,
                'date'        => current_time( 'mysql' )
            );
            update_option( 'jde_kiosques_partner_codes', $partner_codes );
        }

        wp_redirect( admin_url( 'admin.php?page=jde-kiosques' ) );
        exit;
    }
}
