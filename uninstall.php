<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Vérification des permissions de l'utilisateur
if ( ! current_user_can( 'delete_plugins' ) ) {
    exit();
}

// Suppression des options du plugin
delete_option( 'jde_kiosques_total' );
delete_option( 'jde_kiosques_restrict_access' );

// Suppression des transients pour nettoyer le cache
delete_transient( 'jde_kiosques_list' );

// Suppression du rôle personnalisé "Organisateur"
remove_role( 'organisateur' );

// Suppression de la table des réservations
global $wpdb;
$table_name = $wpdb->prefix . 'jde_kiosques_reservations';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// Suppression des fichiers de logs
$logs_dir = WP_CONTENT_DIR . '/uploads/jde-kiosques-logs/';
if ( file_exists( $logs_dir ) ) {
    array_map( 'unlink', glob( "$logs_dir/*" ) ); // Supprime les fichiers
    rmdir( $logs_dir ); // Supprime le dossier
}
