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
define( 'JDE_KIOSQUES_VERSION', '1.2.4' );
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

// Initialisation des classes du plugin
function jde_kiosques_init() {
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
