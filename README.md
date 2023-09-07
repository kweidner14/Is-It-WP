# Is It WP Checker

## Description

The **Is It WP Checker** is a versatile WordPress plugin that enables users to ascertain if a specific website is constructed using WordPress.com or WordPress.org. It's an invaluable tool for web developers, designers, and marketers keen on understanding a website's foundational technology.

**Version:** 1.2.0  
**Author:** Kyle Weidner

## Features

- **Simple User Interface**: Easily input the URL and click "Check".
- **Admin Dashboard**: Find a dedicated section under the "Tools" category in the WordPress dashboard.
- **Data Storage**: The plugin now records URL entries and IP addresses of rate-limited users into the database.
- **Data Export**: Admins have the capability to export URLs and IP addresses as distinct CSV files.
- **Enhanced Design**: Integrated a new stylesheet for polished plugin interfaces.
- **Code Improvement**: Major code refactoring for modularization and maintainability.
- **Rate Limiting**: Users can only conduct a specific number of checks within a certain timeframe, and IP addresses of users who exceed this limit are stored.
- **Heightened Security**: Employs WordPress nonces for form submission, improved input/output sanitization, and measures to counteract SQL injection attacks.

## Installation

### Method 1:
1. Download the plugin.
2. Upload the zipped plugin through the 'Plugins' menu in WordPress.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Implement the shortcode `[isitwp_check]` within your posts or pages to showcase the form.

### Method 2:
1. Download the plugin and extract its contents.
2. Transfer the complete `is-it-wp-checker/` directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Integrate the shortcode `[isitwp_check]` in your content to showcase the form.

## Usage

1. Once the plugin is operational, navigate to any post or page and incorporate the shortcode `[isitwp_check]`.
2. Visit the said page, where you'll be greeted with a form requesting a URL.
3. Input the desired website URL and hit "Check".
4. The plugin will then scrutinize the URL, revealing whether the website utilizes WordPress.com or WordPress.org.

## Security Measures

- **Form Safety**: Utilizes WordPress nonces for form submission, thwarting CSRF assaults.
- **Input/Output Protection**: Enforces rigorous input/output sanitization.
- **SQL Injection Prevention**: Advanced measures have been implemented to prevent potential SQL injection attacks.
- **Rate Limiting**: Records the IP addresses of users who frequently surpass the set check limit.

## Changelog

### Version 1.2.0

- Introduced an admin dashboard section under "Tools".
- Enabled storing of URL entries and rate-limited user IP addresses in the database.
- Granted admins the ability to export data (URLs & IPs) as CSV files.
- Incorporated a new stylesheet for enhanced plugin interface aesthetics.
- Undertook a major code refactor for modularization.
- Elevated security levels with input/output sanitization and SQL injection prevention measures.

### Version 1.1.2

- Incorporated rate-limiting capabilities.
- Boosted security through WordPress nonce utilization.

### Version 1.1.1

- Resolved minor issues.

### Version 1.1.0

- Incorporated a feature to differentiate between WordPress.com and WordPress.org.

### Version 1.0.0

- Initial product release.

## License

This plugin is licensed under the MIT license. Comprehensive license details can be located within the LICENSE.txt document.
