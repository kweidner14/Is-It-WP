<?php
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