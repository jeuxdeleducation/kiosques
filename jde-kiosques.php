<?php
/*
Plugin Name: JDE Kiosques
Description: Plugin de gestion des kiosques pour Jeux de l'Éducation.
Version: 1.2.0
Author: Samuel Lavoie
Author URI: https://github.com/jeuxdeleducation
License: GPL2
GitHub Plugin URI: https://github.com/jeuxdeleducation/kiosques
Plugin URI: https://github.com/jeuxdeleducation/kiosques
Requires PHP: 7.2
Requires at least: 5.0
Tested up to: 6.4
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Vérification de la version de PHP
if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {
    function jde_kiosques_admin_notice() {
        echo '<div class="error"><p>' . __( 'JDE Kiosques nécessite PHP 7.2 ou supérieur.', 'jde-kiosques' ) . '</p></div>';
    }
    add_action( 'admin_notices', 'jde_kiosques_admin_notice' );
    return;
}

// Chargement des fichiers nécessaires
require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-public.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-logs.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-settings.php';

JDE_Kiosques_Admin::init();
JDE_Kiosques_Settings::init();
JDE_Kiosques_Logs::init();

/**
 * Enqueue des scripts et styles uniquement sur la page des kiosques
 */
function jde_kiosques_enqueue_scripts() {
    if ( is_page( 'kiosques' ) ) {
        wp_enqueue_script(
            'jde-kiosques-script',
            plugin_dir_url(__FILE__) . 'assets/script.js',
            array('jquery'),
            '1.2',
            true
        );

        wp_localize_script(
            'jde-kiosques-script',
            'jdeKiosquesAjax',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jde-kiosques-nonce' )
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'jde_kiosques_enqueue_scripts' );

/**
 * Ajout d'une page de configuration pour le plugin
 */
function jde_kiosques_add_settings_page() {
    add_options_page(
        'JDE Kiosques',
        'JDE Kiosques',
        'manage_options',
        'jde-kiosques-settings',
        array( 'JDE_Kiosques_Settings', 'render_settings_page' )
    );
}
add_action( 'admin_menu', 'jde_kiosques_add_settings_page' );

/**
 * Système de logs pour enregistrer les actions majeures
 */
function jde_kiosques_log_action($message) {
    JDE_Kiosques_Logs::add_log($message);
}

/**
 * Gestion des mises à jour automatiques via GitHub Updater
 */
add_filter( 'github_updater_override_dot_org', '__return_true' );
