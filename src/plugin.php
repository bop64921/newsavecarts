<?php

namespace SaveCarts;

use SaveCarts\Setup\Installer;
use SaveCarts\Core\CartSaver;
use SaveCarts\Frontend\PublicView;

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
            new PublicView();
            new CartSaver();
        }
    }

    public static function activate()
    {
        Installer::create_saved_carts_table();
        Installer::create_saved_carts_page();
        update_option('save_carts_activated', true);
    }

    public static function deactivate()
    {
        // Code to run on plugin deactivation
    }
}