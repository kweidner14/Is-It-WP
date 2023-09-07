<?php
// Check if custom database table exists, if not, create one.
// Create new database table
function isitwp_create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'isitwp_urls';
    $table_name_2 = $wpdb->prefix . 'isitwp_ips';

    // Check if table already exists
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(255) NOT NULL,
            result TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_2)) != $table_name_2) {
        $sql2 = "CREATE TABLE $table_name_2 (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            ip TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql2 );
    }
}
// Run the function when the plugin is activated
register_activation_hook( __FILE__, 'isitwp_create_tables' );

function insert_ip_to_db($suffix, $user_ip_concatenated) {
    // Insert IP into database table
    global $wpdb;
    $table_name = $wpdb->prefix . $suffix;
    $wpdb->insert(
        $table_name,
        array(
            'ip' => $user_ip_concatenated
        )
    );
}

function insert_url_to_db($suffix, $url, $db_output) {
    global $wpdb;
    $table_name = $wpdb->prefix . $suffix;
    $wpdb->insert(
        $table_name,
        array(
            'url' => $url,
            'result' => $db_output
        )
    );
}