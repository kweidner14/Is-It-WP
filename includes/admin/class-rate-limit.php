<?php
function rate_limiting()
{
    // Define the rate limit
    $rate_limit = 10; // 10 requests
    $rate_time = 60;  // within 60 seconds

    // Concatenate various sources of IPs
    $ip_keys = ['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR'];
    $user_ip_concatenated = '';

    foreach ($ip_keys as $key) {
        if (isset($_SERVER[$key])) {
            $user_ip_concatenated .= $_SERVER[$key];
        }
    }

    // Hash the concatenated IPs for a more secure and uniform key
    $rate_limit_key = 'rate_limit_' . md5($user_ip_concatenated);

    // Rate Limiting
    $rate_limit_count = get_transient($rate_limit_key);

    // Check if limit has been reached
    if ($rate_limit_count > $rate_limit) {
        // Insert IP into database table
        insert_ip_to_db(esc_html('isitwp_ips'), $user_ip_concatenated);
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
