# Is It WP Checker

## Description

The **Is It WP Checker** is a simple WordPress plugin that allows users to check if a given website is built with WordPress.com or WordPress.org. This is particularly useful for web developers, designers, and marketers who want to get insights about a website's underlying technology.

**Version:** 1.1.2  
**Author:** Kyle Weidner

## Features

- Simple user interface: Just enter the URL and click "Check".
- Rate-limited to prevent abuse: Users can only perform a limited number of checks within a given time.
- Secure: Uses WordPress nonces for form submission.

## Installation

###Method 1:
1. Download the plugin.
2. Upload the zipped plugin through the 'Plugins' menu in WordPress
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Use the shortcode `[isitwp_check]` in your posts or pages to display the form.

###Method 2:
1. Download the plugin and unzip it.
2. Upload the entire `is-it-wp-checker/` directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Use the shortcode `[isitwp_check]` in your posts or pages to display the form.

## Usage

1. After the plugin is activated, go to any post or page and add the shortcode `[isitwp_check]`.
2. Visit the page, and you'll see a form asking for a URL.
3. Enter the URL of the website you want to check and click "Check".
4. The plugin will analyze the URL and display whether the website is built with WordPress.com or WordPress.org.

## Security Measures

- The plugin uses WordPress nonces for form submission to prevent CSRF attacks.
- Input sanitization is performed on form fields.
- Rate-limited to prevent abuse: Users can only perform a limited number of checks within a given time.

## Changelog

### Version 1.1.2

- Added rate-limiting features.
- Improved the security by implementing WordPress nonces.

### Version 1.1.1

- Fixed minor bugs.

### Version 1.1.0

- Added feature to distinguish between WordPress.com and WordPress.org.

### Version 1.0.0

- Initial release.

## License

This plugin is licensed under the MIT license. License information can be found in the LICENSE.txt file.
