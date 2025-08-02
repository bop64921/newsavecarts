<?php

namespace SaveCarts;

use SaveCarts\Setup\Installer;
use SaveCarts\Core\CartSaver;
use SaveCarts\Frontend\PublicView;
use SaveCarts\Core\HookFallback;
use SaveCarts\Admin\Admin;

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
    if (defined('DOING_AJAX') && DOING_AJAX) {
        new CartSaver();  // 👈 cargarlo solo para AJAX
    } elseif (is_admin()) {
        new Admin();
    } else {
        new PublicView();
        new CartSaver();  // 👈 y también en frontend
        HookFallback::init();
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