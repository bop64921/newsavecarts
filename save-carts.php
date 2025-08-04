<?php
/*
Plugin Name: Save Carts
Plugin URI: https://dotados.es
Description: Allows users to save and retrieve their WooCommerce shopping carts.
Version: 1.0.0
Author: Dotados Solutions
Author URI: https://dotados.es
License: GPL2
Text Domain: save-carts
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SAVE_CARTS_PRO')) {
    define('SAVE_CARTS_PRO', false);
}

// Autoload via Composer (if using)
require_once __DIR__ . '/vendor/autoload.php';
use SaveCarts\Plugin;
define('SAVE_CARTS_PLUGIN_URL', plugin_dir_url(__FILE__));
// Register activation and deactivation hooks
register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);

// Run the plugin
function save_carts_run_plugin() {
    new Plugin();
}
save_carts_run_plugin();
?>