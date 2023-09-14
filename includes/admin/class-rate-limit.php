<?php
function rate_limiting()
{
    // Define the rate limit
    $rate_limit = 10; // 10 requests
    $rate_time = 60;  // within 60 seconds

    // Get the user's IP
    // Note: May need to adjust this in the future to deal with VPNs and bots/bad actors
    $ip = $_SERVER['REMOTE_ADDR'];

    // Hash the IPs for a more secure and uniform key
    $rate_limit_key = 'rate_limit_' . md5($ip);

    // Rate Limiting
    $rate_limit_count = get_transient($rate_limit_key);

    // Check if limit has been reached
    if ($rate_limit_count > $rate_limit) {
        // Insert IP into database table
        insert_ip_to_db(esc_html('isitwp_ips'), $ip);
        return [
            'status' => 'limited',
            'message' => "You have reached your rate limit. Please wait before trying again.",
            'rate_limit_key' => $rate_limit_key,
            'rate_limit_count' => $rate_limit_count,
            'rate_time' => $rate_time
        ];
    }

    // Limit has not been reached.
    return [
        'status' => 'ok',
        'rate_limit_key' => $rate_limit_key,
        'rate_limit_count' => $rate_limit_count,
        'rate_time' => $rate_time
    ];
}
