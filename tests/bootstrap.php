<?php
/**
 * Bootstrap for Convoca Theme unit tests.
 */
// Load composer autoload
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Define ABSPATH for theme files
if (!defined('ABSPATH')) { define('ABSPATH', dirname(__DIR__) . '/'); }
if (!defined('WP_DEBUG')) { define('WP_DEBUG', true); }

// Mock WP functions needed by theme
if (!function_exists('__')) { function __($t, $d = 'default') { return $t; } }
if (!function_exists('esc_html')) { function esc_html($t) { return htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); } }
if (!function_exists('esc_attr')) { function esc_attr($t) { return htmlspecialchars($t, ENT_QUOTES, 'UTF-8'); } }
if (!function_exists('add_action')) { function add_action($h, $c, $p = 10, $a = 1) { return true; } }
if (!function_exists('add_filter')) { function add_filter($h, $c, $p = 10, $a = 1) { return true; } }
if (!function_exists('add_theme_support')) { function add_theme_support($f, ...$a) { return true; } }
if (!function_exists('register_block_style')) { function register_block_style($n, $a) { return true; } }
if (!function_exists('register_block_pattern')) { function register_block_pattern($n, $a) { return true; } }
if (!function_exists('register_block_pattern_category')) { function register_block_pattern_category($n, $a) { return true; } }
if (!function_exists('load_theme_textdomain')) { function load_theme_textdomain($d, $p) {} }
if (!function_exists('get_template_directory')) { function get_template_directory() { return ABSPATH; } }
if (!function_exists('get_template_directory_uri')) { function get_template_directory_uri() { return 'https://example.com/wp-content/themes/convoca'; } }
if (!function_exists('wp_enqueue_style')) { function wp_enqueue_style($h, $s = '', $d = [], $v = '', $m = 'all') {} }
if (!function_exists('wp_enqueue_script')) { function wp_enqueue_script($h, $s = '', $d = [], $v = '', $i = false) {} }
if (!function_exists('wp_register_style')) { function wp_register_style($h, $s, $d = [], $v = '', $m = 'all') { return true; } }
if (!function_exists('add_shortcode')) { function add_shortcode($t, $c) { return true; } }
if (!function_exists('is_admin')) { function is_admin() { return false; } }
if (!function_exists('current_user_can')) { function current_user_can($c, ...$a) { return true; } }
if (!function_exists('get_bloginfo')) { function get_bloginfo($s = '') { return 'Convoca'; } }
if (!function_exists('wp_get_theme')) {
    function wp_get_theme() {
        return new class {
            public function get($k) { return '1.0'; }
            public function get_template() { return 'convoca'; }
            public function exists() { return true; }
        };
    }
}
if (!function_exists('get_theme_file_path')) { function get_theme_file_path($f = '') { return dirname(__DIR__) . ($f ? '/' . $f : ''); } }
if (!function_exists('get_theme_file_uri')) { function get_theme_file_uri($f = '') { return 'https://example.com/wp-content/themes/convoca/' . $f; } }
date_default_timezone_set('Europe/Madrid');
