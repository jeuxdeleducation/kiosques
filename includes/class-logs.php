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
        $log_entry = "[{$date}] " . $message . PHP_EOL;
        file_put_contents( $log_file, $log_entry, FILE_APPEND );
    }
    
    public static function get_logs() {
        $log_file = self::get_log_file();
        return file_get_contents( $log_file );
    }
    
    public static function logs_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Logs JDE Kiosques', 'jde-kiosques' ); ?></h1>
            <pre style="background:#f7f7f7; padding:20px; max-height:500px; overflow:auto;"><?php echo esc_html( self::get_logs() ); ?></pre>
            <form method="post" action="">
                <?php wp_nonce_field( 'jde_kiosques_clear_logs', 'jde_kiosques_clear_logs_nonce' ); ?>
                <input type="hidden" name="jde_kiosques_action" value="clear_logs">
                <input type="submit" class="button" value="<?php _e( 'Effacer les logs', 'jde-kiosques' ); ?>">
            </form>
        </div>
        <?php
        
        if ( isset($_POST['jde_kiosques_action']) && $_POST['jde_kiosques_action'] == 'clear_logs' ) {
            if ( check_admin_referer( 'jde_kiosques_clear_logs', 'jde_kiosques_clear_logs_nonce' ) ) {
                file_put_contents( self::get_log_file(), '' );
                echo '<div class="updated notice"><p>' . __( 'Logs effacés.', 'jde-kiosques' ) . '</p></div>';
            }
        }
    }
}
