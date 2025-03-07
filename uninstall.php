<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Vérification de permission pour éviter les suppressions involontaires
if ( ! current_user_can( 'delete_plugins' ) ) {
    exit();
}

// Suppression des options du plugin
delete_option( 'jde_kiosques_total' );
delete_option( 'jde_kiosques_restrict_access' );

// Suppression des transients liés au plugin
delete_transient( 'jde_kiosques_list' );

global $wpdb;
$table_name = $wpdb->prefix . 'jde_kiosques_reservations';

// Vérification si la table existe avant suppression
if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) {
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

// Suppression du rôle personnalisé "Organisateur"
remove_role( 'organisateur' );

// Suppression des fichiers de logs
$logs_dir = WP_CONTENT_DIR . '/uploads/jde-kiosques-logs/';
if ( file_exists( $logs_dir ) ) {
    array_map( 'unlink', glob( "$logs_dir/*" ) );
    rmdir( $logs_dir );
}
