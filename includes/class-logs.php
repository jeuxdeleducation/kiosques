<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Logs {
    private static function get_log_file() {
        $log_dir = WP_CONTENT_DIR . '/uploads/jde-kiosques-logs/';
        $log_file = $log_dir . 'jde-kiosques.log';
        
        if ( ! file_exists( $log_dir ) ) {
            mkdir( $log_dir, 0755, true );
        }
        
        if ( ! file_exists( $log_file ) ) {
            file_put_contents( $log_file, '' );
        }
        
        return $log_file;
    }
    
    public static function add_log( $message ) {
        $log_file = self::get_log_file();
        $date = date( 'Y-m-d H:i:s' );
        $log_entry = "[{$date}] " . sanitize_text_field( $message ) . "\n";
        
        // Vérifier si le fichier est trop grand (5 Mo max)
        if ( file_exists( $log_file ) && filesize( $log_file ) > 5 * 1024 * 1024 ) {
            rename( $log_file, $log_file . '-' . time() . '.bak' );
        }
        
        file_put_contents( $log_file, $log_entry, FILE_APPEND | LOCK_EX );
        
        // Ajouter aussi au journal des erreurs de WordPress
        error_log( "JDE Kiosques: " . $message );
    }
}
