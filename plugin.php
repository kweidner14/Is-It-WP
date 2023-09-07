<?php
/*
Plugin Name: Is It WP Checker
Description: A simple plugin to check if a website is built with WordPress.com or WordPress.org
Version: 1.1.0
Author: Kyle Weidner
*/

function isitwp_check($atts = [], $content = null, $tag = '') {
    ob_start();

    // Adding a nonce field
    $nonce = wp_create_nonce('isitwp_check_nonce');
    echo '<form method="post" id="check-wordpress">
        <input type="text" name="url" placeholder="Enter a URL" required>
        <input type="hidden" name="_wpnonce" value="'. esc_attr($nonce) .'">
        <input type="submit" value="Check">
    </form>';

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
        if (wp_verify_nonce($_POST['_wpnonce'], 'isitwp_check_nonce')) {
            // Process your form here
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL); // Sanitizing
            $result = check_wordpress($url);
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

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL); // Sanitizing
    $result = check_wordpress($url);
}

// Create shortcode for the form [isitwp_check]
add_shortcode('isitwp_check', 'isitwp_check');
