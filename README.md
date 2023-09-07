# Is It WP Checker

**Version 1.0**

Is It WP Checker is a simple WordPress plugin that allows users to check if a website is built with WordPress.

## Description

This plugin takes a URL input from the user and checks if the website is built with WordPress. It differentiates between
WordPress.org and WordPress.com sites. If a website is not built using WordPress, it will display an appropriate 
message.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins 
screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the `[isitwp_check]` shortcode in your posts or pages to display the plugin's form.

## Usage

Simply enter the URL of the website you want to check into the form and click the 'Check' button. The plugin will 
analyze the website and display whether it was built using WordPress.com or WordPress.org, or if it doesn't appear to be
built using WordPress.

## FAQ

**Q: Does this plugin differentiate between WordPress.org and WordPress.com sites?**

A: Yes, the plugin checks for certain characteristics specific to both WordPress.org and WordPress.com sites and 
provides a differentiated output accordingly.

**Q: What do I do if the plugin says a website doesn't appear to be built using WordPress, but I know it is?**

A: The plugin checks for certain common characteristics of WordPress sites, but it's possible for a WordPress site to be
configured in a way that it doesn't exhibit these characteristics, or the web host is obscuring these configurations. If
you're certain a site is built with WordPress and the plugin says it isn't, it's likely such a case. You can try 
scanning a few different pages of the website to attempt to product the correct results.

## License

This project is licensed under the MIT License. See the LICENSE file in the project root for more information.


## Changelog

**1.0**

- Initial release.

## Author

Kyle Weidner
