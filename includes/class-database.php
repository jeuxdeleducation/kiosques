<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JDE_Kiosques_Database {
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'jde_kiosques_reservations';
    }
    
    /**
     * Création ou mise à jour de la table des réservations.
     */
    public function create_tables() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$this->table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            kiosk_number INT(10) NOT NULL,
            partner_code VARCHAR(255) NOT NULL,
            reserved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY kiosk_unique (kiosk_number)
        ) $charset_collate;";

        dbDelta( $sql );
    }
    
    /**
     * Suppression de la table des réservations.
     */
    public function drop_tables() {
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS {$this->table_name}" );
    }
}
