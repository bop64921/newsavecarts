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
    exit; // Exit if accessed directly
}

// Load Composer autoload (if using Composer later)
// require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Manually load the main plugin class (for now)
require_once plugin_dir_path(__FILE__) . 'src/Plugin.php';

use SaveCarts\Plugin;

/**
 * Run the plugin
 */
function save_carts_run_plugin() {
    $plugin = new Plugin();
}
save_carts_run_plugin();

/**
 * Register activation and deactivation hooks
 */
register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);