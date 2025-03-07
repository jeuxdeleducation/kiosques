<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Logs {
    private static function get_log_file() {
        $log_file = JDE_KIOSQUES_LOGS_DIR . 'jde-kiosques.log';
        if ( ! file_exists( $log_file ) ) {
            file_put_contents( $log_file, '' );
        }
        return $log_file;
    }
    
    public static function add_log( $message ) {
        $log_file = self::get_log_file();
        $date = date( 'Y-m-d H:i:s' );
        $log_entry = "[{$date}] " . sanitize_text_field( $message ) . PHP_EOL;
        file_put_contents( $log_file, $log_entry, FILE_APPEND );
    }
    
    public static function get_logs( $limit = 100 ) {
        $log_file = self::get_log_file();
        $logs = file( $log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        return array_slice( $logs, -$limit );
    }
    
    public static function logs_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php _e( 'Logs JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <pre style="background:#f7f7f7; padding:20px; max-height:500px; overflow:auto;">
                <?php echo esc_html( implode( "\n", self::get_logs() ) ); ?>
            </pre>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'jde_kiosques_clear_logs', 'jde_kiosques_clear_logs_nonce' ); ?>
                <input type="hidden" name="action" value="jde_kiosques_clear_logs">
                <input type="submit" class="button" value="<?php _e( 'Effacer les logs', 'jde-kiosques' ); ?>">
            </form>
        </div>
        <?php
    }
}

add_action( 'admin_post_jde_kiosques_clear_logs', function() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Accès refusé.', 'jde-kiosques' ) );
    }
    if ( check_admin_referer( 'jde_kiosques_clear_logs', 'jde_kiosques_clear_logs_nonce' ) ) {
        file_put_contents( JDE_Kiosques_Logs::get_log_file(), '' );
        wp_redirect( admin_url( 'options-general.php?page=jde-kiosques-settings&logs_cleared=true' ) );
        exit;
    }
});
