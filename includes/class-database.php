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
    
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$this->table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            kiosk_number int NOT NULL,
            company_name varchar(255) NOT NULL,
            access_code varchar(100) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'en_attente',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY kiosk_number (kiosk_number)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    public function add_reservation( $kiosk_number, $company_name, $access_code ) {
        global $wpdb;
        $result = $wpdb->insert( 
            $this->table_name, 
            array(
                'kiosk_number' => $kiosk_number,
                'company_name' => sanitize_text_field( $company_name ),
                'access_code'  => sanitize_text_field( $access_code ),
                'status'       => 'en_attente',
            ), 
            array( '%d', '%s', '%s', '%s' )
        );
        return $result;
    }
    
    public function update_reservation_status( $id, $status ) {
        global $wpdb;
        $result = $wpdb->update( 
            $this->table_name, 
            array( 'status' => sanitize_text_field( $status ) ),
            array( 'id' => intval( $id ) ),
            array( '%s' ),
            array( '%d' )
        );
        return $result;
    }
    
    public function get_all_reservations() {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY kiosk_number ASC", ARRAY_A );
        return $results;
    }
    
    public function get_reservation_by_kiosk( $kiosk_number ) {
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE kiosk_number = %d", $kiosk_number ), ARRAY_A );
        return $result;
    }
    
    public function delete_reservation( $id ) {
        global $wpdb;
        $result = $wpdb->delete( $this->table_name, array( 'id' => intval( $id ) ), array( '%d' ) );
        return $result;
    }
}
