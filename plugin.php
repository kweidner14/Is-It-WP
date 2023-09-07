<?php
/*
Plugin Name: Is It WP Checker
Description: A simple plugin to check if a website is built with WordPress.com or WordPress.org
Version: 1.1.2
Author: Kyle Weidner
*/

/*
 * TODO
 *  2. Improve General Security
 *  3. Style Admin Page
 *  4. Break up code into separate files for easier readability / maintainability
 */

// Enqueue stylesheet
function isitwp_enqueue_style() {
    // Register the style
    wp_register_style( 'isitwp_styles', plugin_dir_url( __FILE__ ) . 'style.css' );

    // Enqueue the style
    wp_enqueue_style( 'isitwp_styles' );
}
add_action( 'wp_enqueue_scripts', 'isitwp_enqueue_style' );

// URL & IP Entry Logging
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

function isitwp_check($atts = [], $content = null, $tag = '') {

    // Define the rate limit
    $rate_limit = 10;  // Max 10 requests
    $rate_time = 60;  // within 60 seconds

    // Concatenate various sources of IPs
    $ip_keys = ['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR'];
    $user_ip_concatenated = '';

    foreach($ip_keys as $key) {
        if (isset($_SERVER[$key])) {
            $user_ip_concatenated .= $_SERVER[$key];
        }
    }

    // Hash the concatenated IPs for a more secure and uniform key
    $rate_limit_key = 'rate_limit_' . md5($user_ip_concatenated);

    // Rate Limiting
    $rate_limit_count = get_transient($rate_limit_key);

    if ($rate_limit_count >= $rate_limit) {
        // Insert URL into database table
        global $wpdb;
        $table_name = $wpdb->prefix . 'isitwp_ips';
        $wpdb->insert(
            $table_name,
            array(
                'ip' => $user_ip_concatenated
            )
        );
        return esc_html("You have reached your rate limit. Please wait before trying again.");
    }

    ob_start();

    // The input field with a nonce
    $nonce = wp_create_nonce('isitwp_check_nonce');
    echo '<form method="post" id="check-wordpress">
        <input type="text" name="url" id="check-input-field" placeholder="Enter a URL" required>
        <input type="hidden" name="_wpnonce" value="'. esc_attr($nonce) .'">
        <input type="submit" value="Check">
    </form>';

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
        if (wp_verify_nonce($_POST['_wpnonce'], 'isitwp_check_nonce')) {
            // Process input from form
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
            $result = check_wordpress($url);

            // Increment rate limit count
            if ($rate_limit_count === false) {
                set_transient($rate_limit_key, 1, $rate_time);
            } else {
                set_transient($rate_limit_key, ($rate_limit_count + 1), $rate_time);
            }

            // Map results to output
            if ($result == 1) {
                $result_output = "This website was built using WordPress.com";
                $db_output = "WordPress.com";
            } elseif ($result == 2) {
                $result_output = "This website was built using WordPress.org";
                $db_output = "WordPress.org";
            } elseif($result == 3) {
                $result_output = "This website doesn't appear to be built using WordPress. Some web hosts attempt to obscure that a website is built with WordPress. If you believe the website is built with WordPress, please try analyzing a different page of the website.";
                $db_output = "Not WordPress";
            } else {
                $result_output = "Invalid URL. Please make sure the URL is correct and try again.";
                $db_output = "Invalid URL";
            }

            // Display results
            echo "<div class='analysis-results'>
            <p><span style='font-weight:bold;'>Website Analyzed:</span> " . esc_html($url) . "</p>
            <p><span style='font-weight:bold;'>Result:</span> " . esc_html($result_output) . "</p>
        </div>";

            // Insert URL into database table
            global $wpdb;
            $table_name = $wpdb->prefix . 'isitwp_urls';
            $wpdb->insert(
                $table_name,
                array(
                    'url' => $url,
                    'result' => $db_output
                )
            );

        } else {
            // The nonce is invalid, do not process the form
            echo esc_html('Security check failed!');
        }
    }
    return ob_get_clean();
}

function check_wordpress($url) {

    // Check if we can use cURL
    if (!function_exists('curl_init')) {
        die('cURL not available!');
    }

    // Initialize cURL
    $ch = curl_init();

    // If no scheme is found in the URL, prepend it with 'http://'
    $parsed_url = parse_url($url);
    if (!isset($parsed_url['scheme'])) {
        $url = 'http://' . $url;
    }

    // Now parse the URL again for a valid 'host'
    $parsed_url = parse_url($url);

    // Check if the website is a subdomain of wordpress.com
    if (strpos($parsed_url['host'], 'wordpress.com') !== false) {
        curl_close($ch);
//        return esc_html("This website was built using WordPress.com");
        return 1;
    }

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
//        return esc_html("Invalid URL. Please make sure the URL is correct and try again.");
        return 4;
    }

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the result as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Setting a timeout in seconds
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Enable SSL certificate verification

    // Try getting the content of the site
    $content = curl_exec($ch);

    // If the attempt with 'http://' fails, try with 'https://'
    if ($content === false) {
        $url = str_replace('http://', 'https://', $url);
        curl_setopt($ch, CURLOPT_URL, $url);
        $content = curl_exec($ch);
    }

    // If both attempts fail, return an error message
    if ($content === false) {
        curl_close($ch);
//        return esc_html("Invalid URL. Please make sure the URL is correct and try again.");
        return 4;
    }

    // Check for WordPress.com-specific script source
    if (strpos($content, 's0.wp.com') !== false) {
        curl_close($ch);
//        return esc_html("This website was built using WordPress.com");
        return 1;
    }

    // Check for WordPress.org specific meta tags and WordPress-specific HTML attributes
    if (strpos($content, '<meta name="generator" content="WordPress') !== false ||
        strpos($content, 'wp-content') !== false ||
        strpos($content, 'wp-includes') !== false) {
        curl_close($ch);
//        return esc_html("This website was built using WordPress.org");
        return 2;
    }

    curl_close($ch);

//    return esc_html("This website doesn't appear to be built using WordPress. Some web hosts attempt to obscure that a website is built with WordPress. If you believe the website is built with WordPress, please try analyzing a different page of the website.");
    return 3;
}

// Function to export URLs and Results to a CSV file
function isitwp_export_to_csv($suffix) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'isitwp_' . $suffix;

    $rows = $wpdb->get_results("SELECT * FROM $table_name");

    if ($rows) {
        if($suffix == 'urls') {
            $filename = 'isitwp_urls_export.csv';
        }

        if($suffix == 'ips') {
            $filename = 'isitwp_ips_export.csv';
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        $fp = fopen('php://output', 'wb');

        // Add the header of the CSV
        if($suffix == 'urls') {
            $header = ['ID', 'URL', 'Result', 'Created At'];
        }
        if($suffix == 'ips') {
            $header = ['ID', 'IP', 'Created At'];
        }

        fputcsv($fp, $header);

        // Add the rows to the CSV
        foreach ($rows as $row) {
            if($suffix == 'urls') {
                fputcsv($fp, [$row->id, $row->url, $row->result, $row->created_at]);
            }

            if($suffix == 'ips') {
                fputcsv($fp, [$row->id, $row->ip, $row->created_at]);
            }
        }

        fclose($fp);
        exit;
    } else {
        echo esc_html('No records found!');
    }
}

// Add admin menu for export function
function isitwp_admin_menu() {
    add_submenu_page(
        'tools.php',
        'IsItWP Export to CSV',
        'IsItWP Export',
        'manage_options',
        'isitwp_export',
        'isitwp_export_admin_page'
    );
}
add_action('admin_menu', 'isitwp_admin_menu');

// Admin page content
function isitwp_export_admin_page() {
    echo '
        <div class="wrap">
            <h1>Export IsItWP Data to CSV</h1>
            <div>
                <h2>Export URLs</h2>
                <form method="post" class="isitwp_export_form">
                    <input type="hidden" name="isitwp_export_urls" value="1" />
                    <input type="submit" value="Download CSV" />
                </form>
            </div>
            <div>
                <h2>Export IPs</h2>
                <form method="post" class="isitwp_export_form">
                    <input type="hidden" name="isitwp_export_ips" value="1" />
                    <input type="submit" value="Download CSV" />
                </form>
            </div>
        </div> ';
}

// Check if export button clicked and then export
function isitwp_export_urls() {
    if ( isset($_POST['isitwp_export_urls']) ) {
        isitwp_export_to_csv('urls');
    }
}
add_action('init', 'isitwp_export_urls');

function isitwp_export_ips() {
    if ( isset($_POST['isitwp_export_ips']) ) {
        isitwp_export_to_csv('ips');
    }
}
add_action('init', 'isitwp_export_ips');

// Create shortcode for the form [isitwp_check]
add_shortcode('isitwp_check', 'isitwp_check');
