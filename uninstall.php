<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Suppression des options du plugin.
delete_option( 'jde_kiosques_total' );
delete_option( 'jde_kiosques_restrict_access' );

// Suppression du rôle personnalisé "Organisateur".
remove_role( 'organisateur' );

// Suppression de la table des réservations.
global $wpdb;
$table_name = $wpdb->prefix . 'jde_kiosques_reservations';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// Suppression des fichiers de logs.
$logs_dir = plugin_dir_path( __FILE__ ) . 'logs/';
if ( is_dir( $logs_dir ) ) {
    $files = glob( $logs_dir . '*' );
    foreach ( $files as $file ) {
        if ( is_file( $file ) ) {
            unlink( $file );
        }
    }
    rmdir( $logs_dir );
}
