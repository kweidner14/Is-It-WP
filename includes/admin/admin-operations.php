<?php
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