<?php
// URL & IP Entry Logging
function isitwp_check($atts = [], $content = null, $tag = '') {

    $rate_limit_result = rate_limiting();

    if ($rate_limit_result['status'] === 'limited') {
        // Handle rate limit exceeded scenario
        echo $rate_limit_result['message'];
        exit;
    } else {
        // Increment rate limit count
        if ($rate_limit_result['rate_limit_count'] === false) {
            set_transient($rate_limit_result['rate_limit_key'], 1, $rate_limit_result['rate_time']);
        } else {
            set_transient($rate_limit_result['rate_limit_key'], ($rate_limit_result['rate_limit_count'] + 1), $rate_limit_result['rate_time']);
        }
    }

    ob_start();

    // The input field with a nonce
    echo '<form method="post" id="check-wordpress">
        <input type="text" name="url" id="check-input-field" placeholder="Enter a URL" required>
        <input type="hidden" name="_wpnonce" value="'. esc_attr($nonce) .'">
        <input type="submit" value="Check">
    </form>';

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
            // Process input from form
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
            $result = check_wordpress($url);

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
            insert_url_to_db(esc_html('isitwp_urls'), $url, $db_output);


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
        return 1;
    }

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
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
        return 4;
    }

    // Check for WordPress.com-specific script source
    if (strpos($content, 's0.wp.com') !== false) {
        curl_close($ch);
        return 1;
    }

    // Check for WordPress.org specific meta tags and WordPress-specific HTML attributes
    if (strpos($content, '<meta name="generator" content="WordPress') !== false ||
        strpos($content, 'wp-content') !== false ||
        strpos($content, 'wp-includes') !== false) {
        curl_close($ch);
        return 2;
    }

    curl_close($ch);

    return 3;
}