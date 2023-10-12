# Is It WP Checker

## Description

The **Is It WP Checker** is a WordPress plugin that enables users to determine if a given website is built using WordPress.com or WordPress.org. This tool is invaluable for web developers, designers, and marketers who want to understand the foundational technology of a website.

**Version:** 1.2.1  
**Author:** Kyle Weidner

## Features

- **Simple User Interface**: Simply input a URL and click "Check".
- **Admin Dashboard**: Locate a dedicated section under the "Tools" category in the WordPress dashboard.
- **Data Storage**: Stores URL entries and IP addresses of rate-limited users into the database.
- **Data Export**: Admins can export URL and IP address data as separate CSV files.
- **Enhanced Design**: A new stylesheet has been integrated for a polished plugin interface.
- **Code Improvement**: Major code refactoring for better modularization and maintainability.
- **Rate Limiting**: A certain number of checks within a specific timeframe are allowed, and IP addresses exceeding this limit are stored.

## Installation

### Method 1:
1. Download the plugin.
2. Upload the zipped plugin through the 'Plugins' menu in WordPress.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Implement the shortcode `[isitwp_check]` on your posts or pages to display the form.

### Method 2:
1. Download the plugin and extract the contents.
2. Transfer the entire `is-it-wp-checker/` directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Use the shortcode `[isitwp_check]` in your content to display the form.

## Usage

1. After activating the plugin, go to any post or page and use the shortcode `[isitwp_check]`.
2. Visit the page, where you will find a form asking for a URL.
3. Enter the URL of the website you want to check and click "Check".
4. The plugin will then analyze the URL and inform you whether the website uses WordPress.com or WordPress.org.

## Security Measures

- **Input/Output Protection**: Applies strict input/output sanitization.
- **SQL Injection Prevention**: Employed measures to prevent potential SQL injection attacks.
- **Rate Limiting**: Logs the IP addresses of users who frequently exceed the specified check limit.

## Changelog

### Version 1.2.1

- Removed nonce check for broad accessibility and user experience improvement.

### Version 1.2.0

- Added an admin dashboard section under "Tools".
- Enabled storage of URL entries and rate-limited user IP addresses in the database.
- Enabled admins to export data (URLs & IPs) as CSV files.
- Introduced a new stylesheet for enhanced plugin interface design.
- Major code refactor for better modularization.
- Implemented enhanced security measures for input/output sanitization and SQL injection prevention.

### Version 1.1.2

- Added rate-limiting features.
- Enhanced security through WordPress nonce utilization.

### Version 1.1.1

- Fixed minor bugs.

### Version 1.1.0

- Added a feature to distinguish between WordPress.com and WordPress.org.

### Version 1.0.0

- Initial release.

## License

This plugin is licensed under the MIT License. Comprehensive license details can be found in the LICENSE.txt file.
