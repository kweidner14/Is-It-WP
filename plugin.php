<?php
/*
Plugin Name: Is It WP Checker
Description: A simple plugin to check if a website is built with WordPress.com or WordPress.org
Version: 1.1.2
Author: Kyle Weidner
*/

// Enqueue stylesheet
function isitwp_enqueue_style() {
    // Register the style
    wp_register_style( 'isitwp_styles', plugin_dir_url( __FILE__ ) . 'style.css' );

    // Enqueue the style
    wp_enqueue_style( 'isitwp_styles' );
}
add_action( 'wp_enqueue_scripts', 'isitwp_enqueue_style' );

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
        // Logging the rate-limit violation could be useful to identify potentially malicious users
        error_log("Rate limit reached for IP: " . $user_ip_concatenated);
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

            // Display results
            echo "<div class='analysis-results'>
            <p><span style='font-weight:bold;'>Website Analyzed:</span> " . esc_html($url) . "</p>
            <p><span style='font-weight:bold;'>Result:</span> " . esc_html($result) . "</p>
        </div>";
        } else {
            // The nonce is invalid, do not process the form
            echo 'Security check failed!';
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
        return esc_html("This website was built using WordPress.com");
    }

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return esc_html("Invalid URL. Please make sure the URL is correct and try again.");
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
        return esc_html("Invalid URL. Please make sure the URL is correct and try again.");
    }

    // Check for WordPress.com-specific script source
    if (strpos($content, 's0.wp.com') !== false) {
        curl_close($ch);
        return esc_html("This website was built using WordPress.com");
    }

    // Check for WordPress.org specific meta tags and WordPress-specific HTML attributes
    if (strpos($content, '<meta name="generator" content="WordPress') !== false ||
        strpos($content, 'wp-content') !== false ||
        strpos($content, 'wp-includes') !== false) {
        curl_close($ch);
        return esc_html("This website was built using WordPress.org");
    }

    curl_close($ch);

    return esc_html("This website doesn't appear to be built using WordPress. Some web hosts attempt to obscure that a website is built with WordPress. If you believe the website is built with WordPress, please try analyzing a different page of the website.");
}

// Create shortcode for the form [isitwp_check]
add_shortcode('isitwp_check', 'isitwp_check');
