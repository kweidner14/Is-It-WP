<?php
/*
Plugin Name: Is It WP Checker
Description: A simple plugin to check if a website is built with WordPress.com or WordPress.org
Version: 1.2.0
Author: Kyle Weidner
*/

// Include the necessary files
include_once('includes/core/database-operations.php');
include_once('includes/admin/class-rate-limit.php');
include_once('includes/public/class-website-checker.php');
include_once('includes/admin/class-export.php');
include_once('includes/admin/admin-operations.php');

isitwp_create_tables();

// Enqueue stylesheet
function isitwp_enqueue_style() {
    // Register the style
    wp_register_style( 'isitwp_styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );

    // Enqueue the style
    wp_enqueue_style( 'isitwp_styles' );
}
add_action( 'wp_enqueue_scripts', 'isitwp_enqueue_style' );

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

// Create shortcode for the form [isitwp_check]
add_shortcode('isitwp_check', 'isitwp_check');
