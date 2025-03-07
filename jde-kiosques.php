<?php
/*
Plugin Name: JDE Kiosques
Description: Plugin de gestion des kiosques pour Jeux de l'Éducation.
Version: 1.2.4
Author: Samuel Lavoie
Author URI: https://github.com/jeuxdeleducation
License: GPL2
GitHub Plugin URI: https://github.com/jeuxdeleducation/kiosques
Plugin URI: https://github.com/jeuxdeleducation/kiosques
Requires PHP: 7.4
Requires at least: 5.5
Tested up to: 6.4
Text Domain: jde-kiosques
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Empêche l'accès direct
}

// Vérification de la version PHP
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    function jde_kiosques_php_version_error() {
        echo '<div class="error"><p>' . __( 'JDE Kiosques requiert PHP 7.4 ou supérieur.', 'jde-kiosques' ) . '</p></div>';
    }
    add_action( 'admin_notices', 'jde_kiosques_php_version_error' );
    return;
}

// Définition des constantes
define( 'JDE_KIOSQUES_VERSION', '1.2.3' );
define( 'JDE_KIOSQUES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JDE_KIOSQUES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JDE_KIOSQUES_LOGS_DIR', WP_CONTENT_DIR . '/uploads/jde-kiosques-logs/' );

// Chargement automatique des classes
spl_autoload_register( function( $class ) {
    if ( strpos( $class, 'JDE_Kiosques_' ) === 0 ) {
        $class_file = JDE_KIOSQUES_PLUGIN_DIR . 'includes/class-' . strtolower( str_replace( 'JDE_Kiosques_', '', $class ) ) . '.php';
        if ( file_exists( $class_file ) ) {
            require_once $class_file;
        }
    }
});

// Vérification d'accès global pour les utilisateurs
function jde_kiosques_user_has_access() {
    if ( ! function_exists( 'wp_get_current_user' ) ) {
        require_once ABSPATH . 'wp-includes/pluggable.php';
    }

    if ( current_user_can( 'manage_options' ) ) {
        return true;
    }
    
    $authorized_users = get_option( 'jde_kiosques_authorized_users', array() );
    return in_array( get_current_user_id(), (array) $authorized_users );
}

// Initialisation des classes uniquement si l'utilisateur a accès
function jde_kiosques_init() {
    if ( ! jde_kiosques_user_has_access() ) {
        return;
    }

    new JDE_Kiosques_Admin();
    new JDE_Kiosques_Ajax();
    new JDE_Kiosques_Public();
    new JDE_Kiosques_Settings();
    new JDE_Kiosques_Widget();
    
    load_plugin_textdomain( 'jde-kiosques', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'jde_kiosques_init' );

// Activation du plugin
function jde_kiosques_activate() {
    require_once JDE_KIOSQUES_PLUGIN_DIR . 'includes/class-database.php';
    $db = new JDE_Kiosques_Database();
    $db->create_tables();
    
    // Création du dossier de logs
    if ( ! file_exists( JDE_KIOSQUES_LOGS_DIR ) ) {
        mkdir( JDE_KIOSQUES_LOGS_DIR, 0755, true );
    }
}
register_activation_hook( __FILE__, 'jde_kiosques_activate' );
