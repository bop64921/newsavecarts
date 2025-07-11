<?php

namespace SaveCarts;
use SaveCarts\Setup\Installer;
if (!defined('ABSPATH')) {
    exit;
}

class Plugin
{
    public function __construct()
    {
        // Runs after all plugins are loaded
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init()
    {
        if (!is_admin()) {
        new \SaveCarts\Frontend\PublicView();
        new \SaveCarts\Core\CartSaver();
    }
    }

    public static function activate()
    {
         Installer::create_saved_carts_table();
         Installer::create_saved_carts_page();
        // Optional: flag in DB to know plugin was activated
        update_option('save_carts_activated', true);
    }

    public static function deactivate()
    {
        // Code to run on plugin deactivation
    }


}