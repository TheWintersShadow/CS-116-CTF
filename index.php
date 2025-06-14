<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Polyfill for get_magic_quotes_gpc() function which was removed in PHP 8.0
 */
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        return false;
    }
}

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
