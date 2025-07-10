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

// Autoload via Composer (if using)
require_once plugin_dir_path(__FILE__) . 'src/Plugin.php';
require_once plugin_dir_path(__FILE__) . 'src/Setup/Installer.php';
require_once plugin_dir_path(__FILE__) . 'src/Frontend/PublicView.php';
use SaveCarts\Plugin;

// Register activation and deactivation hooks
register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);

// Run the plugin
function save_carts_run_plugin() {
    new Plugin();
}
save_carts_run_plugin();
?>