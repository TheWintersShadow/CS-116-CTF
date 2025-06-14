<?php
/**
 * Polyfill for get_magic_quotes_gpc() function which was removed in PHP 8.0
 */
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        return false;
    }
}